<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function handleCallback()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = app()->environment('production');

        $notif = new Notification;

        $signature = hash('sha512', $notif->order_id.$notif->status_code.$notif->gross_amount.'.'.config('midtrans.server_key'));
        if ($signature !== $notif->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('invoice_number', $notif->order_id)->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $notif->transaction_status;
        $fraud = $notif->fraud_status;

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $order->load('items');
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
            $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
        } else {
            match (true) {
                $transactionStatus === 'capture' && $fraud === 'accept' => $order->update(['payment_status' => 'paid', 'status' => 'processing']),

                $transactionStatus === 'capture' && $fraud === 'challenge' => $order->update(['payment_status' => 'challenge']),

                $transactionStatus === 'settlement' => $order->update(['payment_status' => 'paid', 'status' => 'processing']),

                $transactionStatus === 'pending' => null,

                default => null,
            };
        }

        return response()->json(['message' => 'OK']);
    }
}
