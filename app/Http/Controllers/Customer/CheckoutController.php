<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cartItems = $cart->items()->with(['product.images', 'product.discount'])->get();

        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->product?->has_discount ? $item->product->discount->value : $item->product?->price;

            return $price * $item->quantity;
        });

        $vat = 1000;
        $addresses = Address::where('user_id', Auth::id())->latest()->get();

        $discount = 0;
        $coupon = null;

        if (session()->has('coupon.id')) {
            $coupon = Coupon::find(session('coupon.id'));

            if ($coupon) {
                if ($coupon->expired_at && $coupon->expired_at->isPast()) {
                    session()->forget('coupon');
                    $coupon = null;
                } elseif ($subtotal < $coupon->minimum_purchase) {
                    session()->forget('coupon');
                    $coupon = null;
                } else {
                    if ($coupon->discount_type === 'fixed') {
                        $discount = min($coupon->discount_value, $subtotal);
                    } else {
                        $discount = min($subtotal * $coupon->discount_value / 100, $subtotal);
                    }
                }
            } else {
                session()->forget('coupon');
            }
        }

        $total = $subtotal - $discount + $vat;

        return view('customer.checkout', compact('cartItems', 'subtotal', 'vat', 'total', 'discount', 'coupon', 'addresses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:midtrans,cod',
        ]);

        $address = Address::where('user_id', Auth::id())->findOrFail($validated['address_id']);

        $cart = Cart::where('user_id', Auth::id())->firstOrFail();
        $cartItems = $cart->items()->with(['product.images', 'product.discount', 'product.category', 'product.brand'])->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->product?->has_discount ? $item->product->discount->value : $item->product?->price;

            return $price * $item->quantity;
        });

        $vat = 1000;
        $discount = 0;
        $coupon = null;

        if (session()->has('coupon.id')) {
            $coupon = Coupon::find(session('coupon.id'));

            if ($coupon) {
                if ($coupon->expired_at && $coupon->expired_at->isPast()) {
                    session()->forget('coupon');
                    $coupon = null;
                } elseif ($subtotal < $coupon->minimum_purchase) {
                    session()->forget('coupon');
                    $coupon = null;
                } else {
                    if ($coupon->discount_type === 'fixed') {
                        $discount = min($coupon->discount_value, $subtotal);
                    } else {
                        $discount = min($subtotal * $coupon->discount_value / 100, $subtotal);
                    }
                }
            } else {
                session()->forget('coupon');
            }
        }

        $total = $subtotal - $discount + $vat;

        $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));

        $itemDetails = $cartItems->map(function ($item) {
            $price = $item->product?->has_discount
                ? $item->product->discount->value
                : ($item->product?->price ?? 0);

            return [
                'id' => (string) $item->product_id,
                'price' => (int) $price,
                'quantity' => (int) $item->quantity,
                'name' => $item->product?->name ?? 'Unknown',
            ];
        })->values()->toArray();

        $snapToken = null;

        if ($validated['payment_method'] === 'midtrans') {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = app()->environment('production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $transaction = [
                'transaction_details' => [
                    'order_id' => $invoiceNumber,
                    'gross_amount' => (int) $total,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => $address->phone,
                ],
                'shipping_address' => [
                    'first_name' => $address->recipient_name,
                    'phone' => $address->phone,
                    'address' => $address->full_address,
                    'city' => $address->city,
                    'postal_code' => $address->postal_code,
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($transaction);
            } catch (\Exception $e) {
                \Log::error('Midtrans Snap Token error: '.$e->getMessage());

                return back()->with('error', 'Payment gateway is temporarily unavailable. Please try again later.');
            }
        }

        try {
            $order = DB::transaction(function () use ($cartItems, $address, $subtotal, $discount, $coupon, $total, $invoiceNumber, $validated, $snapToken) {
                foreach ($cartItems as $item) {
                    if ($item->product) {
                        $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                        if (! $product || $product->stock < $item->quantity) {
                            throw new \Exception("Insufficient stock for {$item->product->name}.");
                        }

                        $product->decrement('stock', $item->quantity);
                    }
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'invoice_number' => $invoiceNumber,
                    'recipient_name' => $address->recipient_name,
                    'phone' => $address->phone,
                    'province' => $address->province,
                    'city' => $address->city,
                    'district' => $address->district,
                    'postal_code' => $address->postal_code,
                    'full_address' => $address->full_address,
                    'coupon_code' => $coupon?->code,
                    'coupon_discount' => $discount,
                    'subtotal' => $subtotal,
                    'shipping_cost' => 0,
                    'total' => $total,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'unpaid',
                    'status' => 'pending',
                    'snap_token' => $snapToken,
                ]);
                foreach ($cartItems as $item) {
                    $sourcePath = $item->product?->images->first()?->image;
                    $imageUrl = asset('image-600x400.png');

                    if ($sourcePath && Storage::disk('public')->exists($sourcePath)) {
                        $filename = pathinfo($sourcePath, PATHINFO_BASENAME);
                        $destPath = 'orders/'.$filename;

                        if (! Storage::disk('public')->exists($destPath)) {
                            Storage::disk('public')->copy($sourcePath, $destPath);
                        }

                        $imageUrl = asset('storage/'.$destPath);
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product?->id,
                        'product_name' => $item->product?->name ?? 'Unknown Product',
                        'image' => $imageUrl,
                        'price' => $item->product?->has_discount
                            ? $item->product->discount->value
                            : ($item->product?->price ?? 0),
                        'quantity' => $item->quantity,
                        'category_name' => $item->product?->category?->name,
                        'brand_name' => $item->product?->brand?->name,
                    ]);
                }

                $cartItems->each->delete();

                if ($coupon) {
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => Auth::id(),
                    ]);
                }

                return $order;
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->forget('coupon');

        NotificationService::send('order_placed', "New order #{$order->invoice_number}", [
            'order_id' => $order->id,
            'invoice' => $order->invoice_number,
            'total' => $order->total,
            'payment_method' => $order->payment_method,
        ]);

        $order->load('items.product');
        foreach ($order->items as $item) {
            if ($item->product) {
                $product = $item->product;
                if ($product->stock === 0) {
                    NotificationService::send('out_of_stock', "{$product->name} is out of stock", [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                    ]);
                } elseif ($product->stock <= 10) {
                    NotificationService::send('low_stock', "{$product->name} stock is low ({$product->stock} left)", [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'stock' => $product->stock,
                    ]);
                }
            }
        }

        return redirect()->route('customer.checkout.confirmation', $order->invoice_number);
    }

    public function confirmation(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load('items');

        return view('customer.order-confirmation', compact('order'));
    }
}
