<?php

use App\Models\Product;
use App\Models\Wishlist;

test('other users cannot remove a wishlist item', function () {
    $product = Product::factory()->create();
    $owner = customer();
    $wishlist = Wishlist::firstOrCreate(['user_id' => $owner->id]);
    $item = $wishlist->items()->create(['product_id' => $product->id]);

    $this->actingAs(customer())
        ->delete(route('customer.wishlist.remove', $item->id))
        ->assertStatus(403);

    expect($item->fresh())->not->toBeNull();
});

test('owners can remove their own wishlist item', function () {
    $product = Product::factory()->create();
    $owner = customer();
    $wishlist = Wishlist::firstOrCreate(['user_id' => $owner->id]);
    $item = $wishlist->items()->create(['product_id' => $product->id]);

    $this->actingAs($owner)
        ->delete(route('customer.wishlist.remove', $item->id))
        ->assertSessionHas('success');

    expect($item->fresh())->toBeNull();
});
