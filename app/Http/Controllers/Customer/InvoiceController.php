<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items');

        $pdf = Pdf::loadView('customer.dashboard.orders.invoice', compact('order'));

        return $pdf->download('invoice-'.$order->invoice_number.'.pdf');
    }
}
