<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->customerRole = Role::create(['name' => 'customer']);
});

test('guest cannot download invoice', function () {
    $order = Order::factory()->create();

    $response = $this->get(route('customer.orders.invoice', $order->invoice_number));

    $response->assertRedirect('/login');
});

test('user cannot download invoice of another user', function () {
    $user = User::factory()->create(['role_id' => $this->customerRole->id]);
    $otherUser = User::factory()->create(['role_id' => $this->customerRole->id]);
    $order = Order::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('customer.orders.invoice', $order->invoice_number));

    $response->assertStatus(403);
});

test('user can download their own invoice as pdf', function () {
    $user = User::factory()->create(['role_id' => $this->customerRole->id]);
    $order = Order::factory()
        ->has(OrderItem::factory()->count(2), 'items')
        ->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('customer.orders.invoice', $order->invoice_number));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader('Content-Disposition', 'attachment; filename=invoice-'.$order->invoice_number.'.pdf');
});
