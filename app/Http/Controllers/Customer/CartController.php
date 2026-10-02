<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Services\LogActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
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

        return view('customer.carts', compact('cartItems', 'subtotal', 'vat', 'total', 'coupon', 'discount'));
    }

    public function store(Request $request, Product $product)
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $quantity = 1;
        if ($request->quantity) $quantity = $request->quantity;
        $cart->items()->firstOrCreate(
            ['product_id' => $product->id],
            ['quantity' => $quantity],
        );

        LogActivityService::log("Added product {$product->name} to cart");

        return back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request)
    {
        $cart = Cart::where('user_id', Auth::id())->firstOrFail();

        foreach ($request->input('quantities', []) as $itemId => $quantity) {
            $item = $cart->items()->find($itemId);
            if ($item) {
                $item->update(['quantity' => min(1000, max(1, (int) $quantity))]);
            }
        }

        LogActivityService::log('Updated cart quantities');

        return back()->with('success', 'Cart updated!');
    }

    public function destroy(CartItem $cartItem)
    {
        abort_if($cartItem->cart->user_id !== Auth::id(), 403);

        LogActivityService::log("Removed product {$cartItem->product->name} from cart");

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart!');
    }
}
