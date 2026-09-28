<?php

use App\Models\Product;

test('search returns empty results for non-string queries', function () {
    $this->getJson('/products/search?q[]=abc')
        ->assertOk()
        ->assertJson(['products' => []]);
});

test('search returns empty results for a blank query', function () {
    $this->getJson('/products/search?q=')
        ->assertOk()
        ->assertJson(['products' => []]);
});

test('search finds products by name', function () {
    Product::factory()->create(['name' => 'Gaming Mouse']);

    $this->getJson('/products/search?q=Mouse')
        ->assertOk()
        ->assertJsonCount(1, 'products');
});
