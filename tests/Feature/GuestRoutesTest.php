<?php

test('guests are redirected from the cart page to login', function () {
    $this->get('/cart')->assertRedirect('/login');
});

test('guests are redirected from the wishlist page to login', function () {
    $this->get('/wishlist')->assertRedirect('/login');
});

test('customers can view their cart page', function () {
    $this->actingAs(customer())
        ->get('/cart')
        ->assertOk();
});

test('customers can view their wishlist page', function () {
    $this->actingAs(customer())
        ->get('/wishlist')
        ->assertOk();
});
