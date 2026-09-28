<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;

function setupUsedCouponCheckout(): array
{
    $user = customer();
    $product = Product::factory()->create(['stock' => 10, 'price' => 50000]);

    $address = Address::create([
        'user_id' => $user->id,
        'recipient_name' => 'Budi',
        'phone' => '081234567890',
        'province' => 'DKI Jakarta',
        'city' => 'Jakarta',
        'district' => 'Menteng',
        'postal_code' => '10310',
        'full_address' => 'Jalan Merdeka No. 1',
    ]);

    $cart = Cart::create(['user_id' => $user->id]);
    CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2]);

    $coupon = Coupon::create([
        'code' => 'SAVE10K',
        'discount_type' => 'fixed',
        'discount_value' => 10000,
        'minimum_purchase' => 0,
    ]);

    return [$user, $product, $address, $coupon];
}

test('user cannot apply a coupon they already used', function () {
    $user = customer();
    $coupon = Coupon::create([
        'code' => 'USED10',
        'discount_type' => 'fixed',
        'discount_value' => 10000,
        'minimum_purchase' => 0,
    ]);
    CouponUsage::create(['coupon_id' => $coupon->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->from(route('cart.index'))
        ->post(route('customer.coupon.apply'), ['coupon_code' => $coupon->code])
        ->assertRedirect(route('cart.index'))
        ->assertSessionHas('error');

    expect(session()->has('coupon'))->toBeFalse();
});

test('user can apply a coupon they have never used', function () {
    $user = customer();
    $coupon = Coupon::create([
        'code' => 'FRESH10',
        'discount_type' => 'fixed',
        'discount_value' => 10000,
        'minimum_purchase' => 0,
    ]);

    $this->actingAs($user)
        ->from(route('cart.index'))
        ->post(route('customer.coupon.apply'), ['coupon_code' => $coupon->code])
        ->assertSessionHas('success');

    expect(session('coupon.id'))->toBe($coupon->id);
});

test('another user can still use the coupon', function () {
    $coupon = Coupon::create([
        'code' => 'SHARE10',
        'discount_type' => 'fixed',
        'discount_value' => 10000,
        'minimum_purchase' => 0,
    ]);
    CouponUsage::create(['coupon_id' => $coupon->id, 'user_id' => customer()->id]);

    $this->actingAs(customer())
        ->from(route('cart.index'))
        ->post(route('customer.coupon.apply'), ['coupon_code' => $coupon->code])
        ->assertSessionHas('success');
});

test('checkout silently drops a coupon the user already used', function () {
    [$user, $product, $address, $coupon] = setupUsedCouponCheckout();
    CouponUsage::create(['coupon_id' => $coupon->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->withSession([
            'checkout_token' => 'token-abc',
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => 'fixed',
                'value' => $coupon->discount_value,
                'minimum' => 0,
                'expired_at' => null,
            ],
        ])
        ->post(route('customer.checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
            'checkout_token' => 'token-abc',
        ])
        ->assertRedirect();

    $order = Order::where('user_id', $user->id)->firstOrFail();

    expect((int) $order->coupon_discount)->toBe(0)
        ->and($order->coupon_code)->toBeNull()
        ->and((int) $order->total)->toBe(101000);

    // Usage tetap hanya satu (tidak bertambah).
    expect(CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $user->id)->count())->toBe(1);
});

test('checkout records coupon usage for a first-time use', function () {
    [$user, $product, $address, $coupon] = setupUsedCouponCheckout();

    $this->actingAs($user)
        ->withSession([
            'checkout_token' => 'token-abc',
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => 'fixed',
                'value' => $coupon->discount_value,
                'minimum' => 0,
                'expired_at' => null,
            ],
        ])
        ->post(route('customer.checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
            'checkout_token' => 'token-abc',
        ])
        ->assertRedirect();

    $order = Order::where('user_id', $user->id)->firstOrFail();

    expect((int) $order->coupon_discount)->toBe(10000)
        ->and(CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $user->id)->count())->toBe(1);
});
