<?php

use App\Models\Cart;

test('owners can reorder a failed order into their cart', function () {
    [$order, $product] = midtransOrder([], stock: 10, quantity: 2);
    $order->update(['payment_status' => 'failed', 'status' => 'pending']);

    $this->actingAs($order->user)
        ->post(route('customer.orders.reorder', $order->invoice_number))
        ->assertRedirect(route('cart.index'))
        ->assertSessionHas('success');

    $cart = Cart::where('user_id', $order->user_id)->first();
    $cartItem = $cart->items()->where('product_id', $product->id)->first();

    expect($cartItem)->not->toBeNull();
    expect((int) $cartItem->quantity)->toBe(2);
});

test('reorder merges into existing cart quantity and clamps to stock', function () {
    [$order, $product] = midtransOrder([], stock: 4, quantity: 3);
    $order->update(['payment_status' => 'failed', 'status' => 'pending']);

    $cart = Cart::firstOrCreate(['user_id' => $order->user_id]);
    $cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);

    $this->actingAs($order->user)
        ->post(route('customer.orders.reorder', $order->invoice_number))
        ->assertSessionHas('success');

    $cartItem = $cart->items()->where('product_id', $product->id)->first();

    expect((int) $cartItem->quantity)->toBe(4);
});

test('reorder skips out-of-stock products and errors when nothing is available', function () {
    [$order, $product] = midtransOrder([], stock: 0, quantity: 2);
    $order->update(['payment_status' => 'failed', 'status' => 'pending']);

    $this->actingAs($order->user)
        ->post(route('customer.orders.reorder', $order->invoice_number))
        ->assertRedirect(route('cart.index'))
        ->assertSessionHas('error');

    $cart = Cart::where('user_id', $order->user_id)->first();
    expect($cart?->items()->count() ?? 0)->toBe(0);
});

test('other users cannot reorder someone elses order', function () {
    [$order] = midtransOrder();

    $this->actingAs(customer())
        ->post(route('customer.orders.reorder', $order->invoice_number))
        ->assertForbidden();
});

test('guests cannot reorder', function () {
    [$order] = midtransOrder();

    $this->post(route('customer.orders.reorder', $order->invoice_number))
        ->assertRedirect('/login');
});
