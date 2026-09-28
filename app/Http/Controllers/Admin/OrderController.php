<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\LogActivityService;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->when($request->search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,unpaid,challenge',
        ]);

        // Validasi kombinasi state supaya tidak korup (stok & pembayaran tidak sinkron).
        if ($validated['payment_status'] === 'paid' && $validated['status'] === 'cancelled') {
            return back()->with('error', 'A paid order cannot be cancelled. Use the payment gateway to refund it first.');
        }

        if ($validated['payment_status'] === 'failed'
            && in_array($validated['status'], ['pending', 'processing', 'shipped', 'completed'], true)) {
            return back()->with('error', 'A failed payment requires the order to be cancelled.');
        }

        // Samakan dengan state machine pembayaran: order yang sudah dibayar langsung diproses.
        if ($validated['payment_status'] === 'paid' && $validated['status'] === 'pending') {
            $validated['status'] = 'processing';
        }

        DB::transaction(function () use ($order, $validated) {
            $locked = Order::query()->whereKey($order->getKey())->lockForUpdate()->first();

            if (! $locked) {
                return;
            }

            $oldStatus = $locked->status;
            $newStatus = $validated['status'];

            $locked->load('items.product');

            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($locked->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                NotificationService::send('order_cancelled', "Order #{$locked->invoice_number} has been cancelled", [
                    'order_id' => $locked->id,
                    'invoice' => $locked->invoice_number,
                ]);
            } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                // Keluar dari cancelled: potong stok kembali (clamp >= 0) supaya tidak dobel saat dibatalkan lagi.
                foreach ($locked->items as $item) {
                    if ($item->product) {
                        $item->product->stock = max(0, (int) $item->product->stock - (int) $item->quantity);
                        $item->product->save();
                    }
                }
            }

            $locked->update($validated);
        });

        LogActivityService::log("Updated order {$order->invoice_number}: status={$validated['status']}, payment={$validated['payment_status']}");

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }

    public function shippingLabel(Order $order)
    {
        $order->load('items');

        $pdf = Pdf::loadView('admin.orders.shipping-label', compact('order'));

        return $pdf->download('shipping-label-'.$order->invoice_number.'.pdf');
    }
}
