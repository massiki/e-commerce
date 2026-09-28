<?php

it('forbids other users from checking payment status', function () {
    [$order] = midtransOrder();
    $other = customer();

    $this->actingAs($other)
        ->get("/customer/orders/{$order->invoice_number}/payment-status")
        ->assertStatus(403);
});

it('syncs payment status from midtrans for the owner', function () {
    [$order] = midtransOrder();
    $owner = $order->user;
    $fake = fakeMidtrans();
    $fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'gross_amount' => '101000.00',
    ]);

    $this->actingAs($owner)
        ->getJson("/customer/orders/{$order->invoice_number}/payment-status")
        ->assertOk()
        ->assertJson([
            'payment_status' => 'paid',
            'status' => 'processing',
            'synced' => true,
        ]);

    expect($order->refresh()->payment_status)->toBe('paid');
});

it('returns current state without syncing when midtrans is unreachable', function () {
    [$order] = midtransOrder();
    $owner = $order->user;
    $fake = fakeMidtrans();
    $fake->statusThrows = true;

    $this->actingAs($owner)
        ->getJson("/customer/orders/{$order->invoice_number}/payment-status")
        ->assertOk()
        ->assertJson([
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'synced' => false,
        ]);
});

it('only calls midtrans once within ten seconds per order', function () {
    [$order] = midtransOrder();
    $owner = $order->user;
    $fake = fakeMidtrans();
    $fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'gross_amount' => '101000.00',
        'transaction_status' => 'pending',
    ]);

    $this->actingAs($owner)
        ->getJson("/customer/orders/{$order->invoice_number}/payment-status")
        ->assertOk()
        ->assertJson(['synced' => true, 'payment_status' => 'unpaid']);

    $this->actingAs($owner)
        ->getJson("/customer/orders/{$order->invoice_number}/payment-status")
        ->assertOk()
        ->assertJson(['synced' => false]);

    expect($fake->statusCalls)->toBe(1);
});
