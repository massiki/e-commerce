<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;

function setupCheckout(int $quantity = 2): array
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
    CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => $quantity]);

    return [$user, $product, $address];
}

it('creates an order whose gross amount equals the item details total', function () {
    [$user, $product, $address] = setupCheckout();
    $fake = fakeMidtrans();

    $response = $this->actingAs($user)
        ->withSession(['checkout_token' => 'token-abc'])
        ->post(route('customer.checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'midtrans',
            'checkout_token' => 'token-abc',
        ]);

    $order = Order::where('user_id', $user->id)->firstOrFail();

    $response->assertRedirect(route('customer.checkout.confirmation', $order->invoice_number));

    expect($order->snap_token)->toBe('fake-snap-token')
        ->and((int) $order->total)->toBe(101000)
        ->and((int) $product->refresh()->stock)->toBe(8);

    $payload = $fake->lastSnapPayload;
    $itemsTotal = array_sum(array_map(
        fn (array $item) => $item['price'] * $item['quantity'],
        $payload['item_details']
    ));

    expect($itemsTotal)->toBe($payload['transaction_details']['gross_amount'])
        ->and($payload['transaction_details']['gross_amount'])->toBe(101000);
});

it('includes coupon discount and tax lines in the snap payload', function () {
    [$user, $product, $address] = setupCheckout();
    $coupon = Coupon::create([
        'code' => 'SAVE20K',
        'discount_type' => 'fixed',
        'discount_value' => 20000,
        'minimum_purchase' => 0,
    ]);
    $fake = fakeMidtrans();

    $this->actingAs($user)
        ->withSession(['checkout_token' => 'token-abc', 'coupon.id' => $coupon->id])
        ->post(route('customer.checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'midtrans',
            'checkout_token' => 'token-abc',
        ]);

    $order = Order::where('user_id', $user->id)->firstOrFail();
    $payload = $fake->lastSnapPayload;
    $itemsTotal = array_sum(array_map(
        fn (array $item) => $item['price'] * $item['quantity'],
        $payload['item_details']
    ));

    expect((int) $order->coupon_discount)->toBe(20000)
        ->and((int) $order->total)->toBe(81000)
        ->and($payload['transaction_details']['gross_amount'])->toBe(81000)
        ->and($itemsTotal)->toBe(81000)
        ->and($payload['item_details'])->toHaveCount(3);
});

it('rejects a second checkout submission with the same token', function () {
    [$user, $product, $address] = setupCheckout();
    fakeMidtrans();

    $data = [
        'address_id' => $address->id,
        'payment_method' => 'midtrans',
        'checkout_token' => 'token-abc',
    ];

    $this->actingAs($user)
        ->withSession(['checkout_token' => 'token-abc'])
        ->post(route('customer.checkout.store'), $data)
        ->assertRedirect();

    $this->actingAs($user)
        ->post(route('customer.checkout.store'), $data)
        ->assertSessionHas('error');

    expect(Order::count())->toBe(1)
        ->and($product->refresh()->stock)->toBe(8);
});

it('rejects checkout without a token', function () {
    [$user, $product, $address] = setupCheckout();
    fakeMidtrans();

    $this->actingAs($user)
        ->withSession(['checkout_token' => 'token-abc'])
        ->post(route('customer.checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'midtrans',
        ])
        ->assertSessionHas('error');

    expect(Order::count())->toBe(0)
        ->and($product->refresh()->stock)->toBe(10);
});

it('creates a cod order without a snap token', function () {
    [$user, $product, $address] = setupCheckout();
    fakeMidtrans();

    $response = $this->actingAs($user)
        ->withSession(['checkout_token' => 'token-abc'])
        ->post(route('customer.checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
            'checkout_token' => 'token-abc',
        ]);

    $order = Order::where('user_id', $user->id)->firstOrFail();

    $response->assertRedirect(route('customer.checkout.confirmation', $order->invoice_number));

    expect($order->snap_token)->toBeNull()
        ->and($order->payment_method)->toBe('cod')
        ->and($product->refresh()->stock)->toBe(8);
});
