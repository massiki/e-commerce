<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\MidtransUnavailableException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransService;
use App\Services\PaymentStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    /**
     * POST /api/payment/notification — webhook dari Midtrans.
     *
     * Alur: verifikasi signature dari payload -> konfirmasi ke API Midtrans
     * -> terapkan transisi status via PaymentStateService (idempoten).
     */
    public function handleCallback(Request $request, MidtransService $midtrans, PaymentStateService $payments)
    {
        $input = $request->all();

        $orderId = $input['order_id'] ?? null;
        $signatureKey = $input['signature_key'] ?? null;

        if (! $orderId || ! $signatureKey || ! isset($input['status_code'], $input['gross_amount'])) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $expected = hash('sha512', $input['order_id'].$input['status_code'].$input['gross_amount'].config('midtrans.server_key'));

        if (! hash_equals($expected, (string) $signatureKey)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        try {
            $status = $midtrans->getStatus((string) $orderId);
        } catch (MidtransUnavailableException) {
            // Balas non-200 agar Midtrans mengirim ulang notifikasi.
            return response()->json(['message' => 'Midtrans unavailable'], 502);
        }

        if (! $status) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if (isset($status->status_code) && (string) $input['status_code'] !== (string) $status->status_code) {
            Log::warning('Midtrans notification status_code differs from API status', [
                'order_id' => $orderId,
                'notification_status_code' => $input['status_code'],
                'api_status_code' => $status->status_code,
            ]);
        }

        $order = Order::where('invoice_number', $status->order_id ?? $orderId)->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $payments->apply($order, $status, 'webhook');

        return response()->json(['message' => 'OK']);
    }
}
