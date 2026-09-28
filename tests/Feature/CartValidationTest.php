<?php

use App\Models\Cart;
use App\Models\Product;

test('adding to cart rejects a negative quantity', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $this->actingAs(customer())
        ->post(route('customer.cart.add', $product->id), ['quantity' => -5])
        ->assertSessionHasErrors('quantity');

    $cart = Cart::where('user_id', auth()->id())->first();
    expect($cart?->items()->count() ?? 0)->toBe(0);
});

test('adding to cart rejects a zero quantity', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $this->actingAs(customer())
        ->post(route('customer.cart.add', $product->id), ['quantity' => 0])
        ->assertSessionHasErrors('quantity');
});

test('adding to cart accepts a valid quantity', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $this->actingAs(customer())
        ->post(route('customer.cart.add', $product->id), ['quantity' => 3])
        ->assertSessionHas('success');

    $cart = Cart::where('user_id', auth()->id())->firstOrFail();
    expect((int) $cart->items()->first()->quantity)->toBe(3);
});

test('updating cart clamps negative quantities to one', function () {
    $user = customer();
    $product = Product::factory()->create(['stock' => 10]);
    $cart = Cart::create(['user_id' => $user->id]);
    $item = $cart->items()->create(['product_id' => $product->id, 'quantity' => 3]);

    $this->actingAs($user)
        ->patch(route('customer.cart.update'), [
            'quantities' => [$item->id => -7],
        ])
        ->assertSessionHas('success');

    expect((int) $item->fresh()->quantity)->toBe(1);
});
