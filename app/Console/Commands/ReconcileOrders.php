<?php

namespace App\Console\Commands;

use App\Exceptions\MidtransUnavailableException;
use App\Models\Order;
use App\Services\MidtransService;
use App\Services\PaymentStateService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('orders:reconcile')]
#[Description('Sinkronisasi pembayaran Midtrans yang tertinggal dan auto-expire order yang belum bayar')]
class ReconcileOrders extends Command
{
    public function handle(MidtransService $midtrans, PaymentStateService $payments): int
    {
        $expireMinutes = (int) config('midtrans.expire_unpaid_minutes');

        $orders = Order::query()
            ->where('payment_method', 'midtrans')
            ->where('status', 'pending')
            ->whereIn('payment_status', ['unpaid', 'pending', 'challenge'])
            ->where('created_at', '<=', now()->subMinutes($expireMinutes))
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            try {
                $status = $midtrans->getStatus($order->invoice_number);
            } catch (MidtransUnavailableException $e) {
                Log::warning('Reconcile: Midtrans status unavailable', [
                    'invoice' => $order->invoice_number,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }

            if ($status && ($status->transaction_status ?? null) === 'pending') {
                try {
                    $status = $midtrans->expire($order->invoice_number);
                } catch (MidtransUnavailableException $e) {
                    Log::warning('Reconcile: Midtrans expire failed', [
                        'invoice' => $order->invoice_number,
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }
            }

            if (! $status) {
                // Transaksi tidak ditemukan di Midtrans (mis. salah environment) — expire lokal.
                $status = (object) [
                    'order_id' => $order->invoice_number,
                    'transaction_id' => null,
                    'status_code' => '404',
                    'transaction_status' => 'expire',
                    'fraud_status' => null,
                    'gross_amount' => $order->total,
                ];
            }

            if ($payments->apply($order, $status, 'reconcile')) {
                $count++;
            }
        }

        $this->info("Reconciled {$count} order(s).");

        return self::SUCCESS;
    }
}
