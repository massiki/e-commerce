<?php

beforeEach(function () {
    $this->fake = fakeMidtrans();
});

it('cancels the midtrans transaction before cancelling the order', function () {
    [$order, $product] = midtransOrder();
    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'transaction_status' => 'pending',
        'fraud_status' => null,
    ]);

    $this->actingAs($order->user)
        ->post("/customer/orders/{$order->invoice_number}/cancel")
        ->assertRedirect('/customer/orders');

    expect($this->fake->lastCancelled)->toBe($order->invoice_number)
        ->and($order->refresh()->status)->toBe('cancelled')
        ->and($order->payment_status)->toBe('failed')
        ->and($product->refresh()->stock)->toBe(12);
});

it('rejects cancellation when the order is already paid', function () {
    [$order, $product] = midtransOrder();
    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'gross_amount' => '101000.00',
    ]);

    $this->actingAs($order->user)
        ->from("/customer/orders/{$order->invoice_number}")
        ->post("/customer/orders/{$order->invoice_number}/cancel")
        ->assertRedirect("/customer/orders/{$order->invoice_number}")
        ->assertSessionHas('error');

    $order->refresh();

    expect($order->payment_status)->toBe('paid')
        ->and($order->status)->toBe('processing')
        ->and($product->refresh()->stock)->toBe(10);
});

it('rejects cancellation when gateway cancel fails', function () {
    [$order, $product] = midtransOrder();
    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'transaction_status' => 'pending',
        'fraud_status' => null,
    ]);
    $this->fake->cancelResult = false;

    $this->actingAs($order->user)
        ->post("/customer/orders/{$order->invoice_number}/cancel")
        ->assertSessionHas('error');

    $order->refresh();

    expect($this->fake->lastCancelled)->toBe($order->invoice_number)
        ->and($order->status)->toBe('pending')
        ->and($order->payment_status)->toBe('unpaid')
        ->and($product->refresh()->stock)->toBe(10);
});

it('cancels a cod order without calling the gateway', function () {
    [$order, $product] = midtransOrder(['payment_method' => 'cod']);

    $this->actingAs($order->user)
        ->post("/customer/orders/{$order->invoice_number}/cancel")
        ->assertRedirect('/customer/orders');

    expect($this->fake->lastCancelled)->toBeNull()
        ->and($order->refresh()->status)->toBe('cancelled')
        ->and($product->refresh()->stock)->toBe(12);
});

it('does not cancel orders that are no longer pending', function () {
    [$order, $product] = midtransOrder(['payment_status' => 'paid', 'status' => 'processing']);

    $this->actingAs($order->user)
        ->post("/customer/orders/{$order->invoice_number}/cancel")
        ->assertSessionHas('error');

    expect($order->refresh()->status)->toBe('processing')
        ->and($product->refresh()->stock)->toBe(10);
});

it('forbids other users from cancelling an order', function () {
    [$order] = midtransOrder();

    $this->actingAs(customer())
        ->post("/customer/orders/{$order->invoice_number}/cancel")
        ->assertStatus(403);
});
