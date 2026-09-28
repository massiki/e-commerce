<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\LogActivityService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $coupons = Coupon::when($request->search, function ($query, $search) {
            $query->where('code', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => ['required', 'numeric', 'min:0', 'max:'.($request->discount_type === 'percent' ? '100' : '99999999')],
            'minimum_purchase' => 'required|numeric|min:0',
            'expired_at' => 'nullable|date',
        ]);

        $coupon = Coupon::create([
            'code' => $request->code,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'minimum_purchase' => $request->minimum_purchase,
            'expired_at' => $request->expired_at,
        ]);

        LogActivityService::log("Created coupon: {$coupon->code}");

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,'.$coupon->id,
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => ['required', 'numeric', 'min:0', 'max:'.($request->discount_type === 'percent' ? '100' : '99999999')],
            'minimum_purchase' => 'required|numeric|min:0',
            'expired_at' => 'nullable|date',
        ]);

        $coupon->update([
            'code' => $request->code,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'minimum_purchase' => $request->minimum_purchase,
            'expired_at' => $request->expired_at,
        ]);

        LogActivityService::log("Updated coupon: {$coupon->code}");

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $couponCode = $coupon->code;

        $coupon->delete();

        LogActivityService::log("Deleted coupon: {$couponCode}");

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
