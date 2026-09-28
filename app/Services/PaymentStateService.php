<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * State machine pembayaran yang idempoten.
 *
 * Satu-satunya tempat transisi payment_status/status terjadi
 * (webhook Midtrans, sinkronisasi manual, dan reconcile semuanya lewat sini).
 */
class PaymentStateService
{
    /**
     * Terapkan status dari Midtrans ke order tertentu.
     *
     * @param  object  $status  objek hasil Transaction::status() / notifikasi Midtrans
     * @param  string  $source  'webhook' | 'sync' | 'reconcile'
     * @return bool true jika ada transisi yang benar-benar terjadi
     */
    public function apply(Order $order, object $status, string $source = 'webhook'): bool
    {
        $payload = json_decode(json_encode($status), true) ?: [];

        return DB::transaction(function () use ($order, $status, $payload, $source) {
            $locked = Order::query()->whereKey($order->getKey())->lockForUpdate()->first();

            if (! $locked) {
                return false;
            }

            // Dedupe: notifikasi/sync/reconcile yang berulang tidak boleh membuat baris Payment ganda.
            Payment::firstOrCreate(
                [
                    'order_id' => $locked->id,
                    'transaction_id' => $status->transaction_id ?? null,
                    'transaction_status' => $status->transaction_status ?? null,
                ],
                [
                    'provider' => 'midtrans',
                    'status_code' => isset($status->status_code) ? (string) $status->status_code : null,
                    'fraud_status' => $status->fraud_status ?? null,
                    'gross_amount' => isset($status->gross_amount) ? (float) $status->gross_amount : null,
                    'payload' => $payload,
                    'paid_at' => $this->paidAt($status),
                ]
            );

            return $this->transition($locked, $status, $source);
        });
    }

    private function paidAt(object $status): ?string
    {
        $tx = $status->transaction_status ?? null;

        if (! in_array($tx, ['settlement', 'capture'], true)) {
            return null;
        }

        return $status->settlement_time ?? $status->transaction_time ?? now()->toDateTimeString();
    }

    private function transition(Order $order, object $status, string $source): bool
    {
        $tx = $status->transaction_status ?? null;
        $fraud = $status->fraud_status ?? null;

        $isPaid = $tx === 'settlement' || ($tx === 'capture' && $fraud === 'accept');

        if ($isPaid) {
            if ($this->amountMismatch($order, $status)) {
                // Log & notifikasi sekali saja per order (webhook bisa berulang tanpa henti).
                if (Cache::add("payment-mismatch:{$order->id}", 1, now()->addDay())) {
                    Log::error('Midtrans amount mismatch — payment ignored', [
                        'invoice' => $order->invoice_number,
                        'order_total' => $order->total,
                        'gross_amount' => $status->gross_amount ?? null,
                        'source' => $source,
                    ]);

                    NotificationService::send('payment_anomaly', "Amount mismatch for order #{$order->invoice_number}", [
                        'order_id' => $order->id,
                        'invoice' => $order->invoice_number,
                        'order_total' => (string) $order->total,
                        'gross_amount' => (string) ($status->gross_amount ?? 'missing'),
                        'reason' => 'amount_mismatch',
                    ]);
                }

                return false;
            }

            return $this->markPaid($order, $source);
        }

        if ($tx === 'capture' && $fraud === 'challenge') {
            if (in_array($order->payment_status, ['paid', 'challenge'], true)) {
                return false;
            }

            $order->update(['payment_status' => 'challenge']);

            LogActivityService::log("Payment challenged for order {$order->invoice_number}");

            return true;
        }

        if (in_array($tx, ['deny', 'cancel', 'expire'], true)) {
            return $this->markFailed($order, $tx, $source);
        }

        // pending / status tak dikenal → tidak ada transisi
        return false;
    }

    private function amountMismatch(Order $order, object $status): bool
    {
        if (! isset($status->gross_amount)) {
            return true;
        }

        return (int) round((float) $status->gross_amount) !== (int) round((float) $order->total);
    }

    private function markPaid(Order $order, string $source): bool
    {
        if ($order->payment_status === 'paid') {
            return false;
        }

        $wasCancelled = $order->status === 'cancelled';

        $order->update([
            'payment_status' => 'paid',
            'status' => in_array($order->status, ['pending', 'cancelled'], true) ? 'processing' : $order->status,
        ]);

        if ($wasCancelled) {
            $this->reapplyStock($order);

            NotificationService::send('payment_anomaly', "Payment received for cancelled order #{$order->invoice_number}", [
                'order_id' => $order->id,
                'invoice' => $order->invoice_number,
                'reason' => 'paid_after_cancelled',
            ]);
        }

        LogActivityService::log("Payment settled for order {$order->invoice_number} ({$source})");

        NotificationService::send('payment_settlement', "Payment settled for order #{$order->invoice_number}", [
            'order_id' => $order->id,
            'invoice' => $order->invoice_number,
        ]);

        return true;
    }

    private function markFailed(Order $order, string $tx, string $source): bool
    {
        // Idempoten: jangan batalkan dua kali (restore stok ganda) dan jangan batalkan order yang sudah dibayar.
        if (in_array($order->payment_status, ['paid', 'failed'], true)) {
            return false;
        }

        $order->load('items.product');

        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->update([
            'payment_status' => 'failed',
            'status' => 'cancelled',
        ]);

        if ($tx === 'expire') {
            LogActivityService::log("Payment expired for order {$order->invoice_number} ({$source})");

            NotificationService::send('payment_expired', "Payment expired for order #{$order->invoice_number}", [
                'order_id' => $order->id,
                'invoice' => $order->invoice_number,
            ]);
        } else {
            LogActivityService::log("Payment failed for order {$order->invoice_number} ({$source})");

            NotificationService::send('payment_failed', "Payment failed for order #{$order->invoice_number}", [
                'order_id' => $order->id,
                'invoice' => $order->invoice_number,
            ]);
        }

        return true;
    }

    /**
     * Potong stok kembali untuk order yang dibatalkan ternyata terbayar (clamp >= 0).
     */
    private function reapplyStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->first();

            if (! $product) {
                continue;
            }

            $product->stock = max(0, (int) $product->stock - (int) $item->quantity);
            $product->save();
        }
    }
}
