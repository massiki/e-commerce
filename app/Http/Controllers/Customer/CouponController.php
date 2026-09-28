<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Services\LogActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (! $coupon) {
            return redirect()->back()->with('error', 'Coupon not found!');
        }

        if ($coupon->expired_at && $coupon->expired_at->isPast()) {
            return redirect()->back()->with('error', 'Coupon has expired!');
        }

        $alreadyUsed = CouponUsage::where('coupon_id', $coupon->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyUsed) {
            return redirect()->back()->with('error', 'You have already used this coupon.');
        }

        $cart = Cart::where('user_id', Auth::id())->first();
        $subtotal = 0;

        if ($cart) {
            $subtotal = $cart->items()->with('product.discount')->get()->sum(function ($item) {
                $price = $item->product?->has_discount
                    ? $item->product->discount->value
                    : $item->product?->price;

                return ($price ?? 0) * $item->quantity;
            });
        }

        if ($subtotal < $coupon->minimum_purchase) {
            return redirect()->back()->with('error', 'Minimum purchase of Rp'.number_format($coupon->minimum_purchase, 0, ',', '.').' required!');
        }

        session([
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->discount_type,
                'value' => $coupon->discount_value,
                'minimum' => $coupon->minimum_purchase,
                'expired_at' => $coupon->expired_at?->toDateTimeString(),
            ],
        ]);

        LogActivityService::log("Applied coupon {$coupon->code}");

        return redirect()->back()->with('success', 'Coupon applied successfully.');
    }

    public function remove()
    {
        session()->forget('coupon');

        LogActivityService::log('Removed coupon from session');

        return redirect()->back()->with('success', 'Coupon removed successfully.');
    }
}
