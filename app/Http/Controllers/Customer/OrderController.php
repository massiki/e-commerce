<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\MidtransUnavailableException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\LogActivityService;
use App\Services\MidtransService;
use App\Services\PaymentStateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('customer.dashboard.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load('items');

        return view('customer.dashboard.orders.show', compact('order'));
    }

    public function paymentStatus(Order $order, MidtransService $midtrans, PaymentStateService $payments)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $payload = [
            'payment_status' => $order->payment_status,
            'status' => $order->status,
            'synced' => false,
        ];

        if ($order->payment_method !== 'midtrans' || in_array($order->payment_status, ['paid', 'failed'], true)) {
            return response()->json($payload);
        }

        try {
            $status = $midtrans->getStatus($order->invoice_number);
        } catch (MidtransUnavailableException) {
            return response()->json($payload);
        }

        if ($status) {
            $payments->apply($order, $status, 'sync');
            $order->refresh();
            $payload['synced'] = true;
            $payload['payment_status'] = $order->payment_status;
            $payload['status'] = $order->status;
        }

        return response()->json($payload);
    }

    public function cancel(Order $order, MidtransService $midtrans, PaymentStateService $payments)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        if (! in_array($order->payment_status, ['unpaid', 'pending'], true)) {
            return back()->with('error', 'This order cannot be cancelled because its payment is already '.ucfirst($order->payment_status).'.');
        }

        // Untuk Midtrans: batalkan transaksi di gateway dulu supaya Snap token
        // yang masih berlaku tidak bisa dipakai membayar order yang sudah dibatalkan.
        if ($order->payment_method === 'midtrans') {
            try {
                $status = $midtrans->getStatus($order->invoice_number);
            } catch (MidtransUnavailableException) {
                return back()->with('error', 'Payment gateway is temporarily unavailable. Please try again later.');
            }

            if ($status && in_array($status->transaction_status ?? null, ['settlement', 'capture'], true)) {
                $payments->apply($order, $status, 'sync');

                return back()->with('error', 'This order has already been paid and cannot be cancelled.');
            }

            if ($status && ($status->transaction_status ?? null) === 'pending' && ! $midtrans->cancel($order->invoice_number)) {
                // Pembatalan di gateway gagal — kemungkinan baru saja terbayar, sinkronkan dulu.
                try {
                    $fresh = $midtrans->getStatus($order->invoice_number);
                } catch (MidtransUnavailableException) {
                    $fresh = null;
                }

                if ($fresh) {
                    $payments->apply($order, $fresh, 'sync');
                }

                return back()->with('error', 'Unable to cancel this order. Please refresh and try again.');
            }
        }

        $cancelled = DB::transaction(function () use ($order) {
            $locked = Order::query()->whereKey($order->getKey())->lockForUpdate()->first();

            if (! $locked
                || $locked->status !== 'pending'
                || ! in_array($locked->payment_status, ['unpaid', 'pending'], true)) {
                return false;
            }

            $locked->load('items.product');

            foreach ($locked->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $locked->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);

            return true;
        });

        if (! $cancelled) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        LogActivityService::log("Cancelled order {$order->invoice_number}");

        return redirect()->route('customer.orders.index')
            ->with('success', 'Order cancelled successfully.');
    }
}
