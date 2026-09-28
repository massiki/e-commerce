<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;

test('users cannot review products they never bought', function () {
    $user = customer();
    $product = Product::factory()->create();

    $this->actingAs($user)
        ->post(route('customer.reviews.store'), [
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Looks nice',
        ])
        ->assertSessionHas('error');

    expect(Review::count())->toBe(0);
});

test('users cannot review items from another users order', function () {
    $product = Product::factory()->create();
    $otherOrder = Order::factory()->create(['status' => 'completed']);
    OrderItem::factory()->create(['order_id' => $otherOrder->id, 'product_id' => $product->id]);

    $this->actingAs(customer())
        ->post(route('customer.reviews.store'), [
            'product_id' => $product->id,
            'rating' => 1,
            'comment' => 'Not mine',
        ])
        ->assertSessionHas('error');

    expect(Review::count())->toBe(0);
});

test('owners can review a completed order item exactly once', function () {
    $user = customer();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

    $payload = [
        'product_id' => $product->id,
        'rating' => 5,
        'comment' => 'Great product',
    ];

    $this->actingAs($user)
        ->post(route('customer.reviews.store'), $payload)
        ->assertSessionHas('success');

    $this->actingAs($user)
        ->post(route('customer.reviews.store'), $payload)
        ->assertSessionHas('error');

    expect(Review::count())->toBe(1);
});

test('reviews of orders that are not completed are rejected', function () {
    $user = customer();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

    $this->actingAs($user)
        ->post(route('customer.reviews.store'), [
            'product_id' => $product->id,
            'rating' => 4,
            'comment' => 'Too early',
        ])
        ->assertSessionHas('error');

    expect(Review::count())->toBe(0);
});
