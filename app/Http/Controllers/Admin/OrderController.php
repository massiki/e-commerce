<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
            'payment_status' => 'required|in:pending,paid,failed,unpaid',
        ]);

        if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
            $order->load('items');
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            NotificationService::send('order_cancelled', "Order #{$order->invoice_number} has been cancelled", [
                'order_id' => $order->id,
                'invoice' => $order->invoice_number,
            ]);
        }

        $order->update($validated);

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
