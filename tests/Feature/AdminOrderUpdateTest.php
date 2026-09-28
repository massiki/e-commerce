<?php

test('cancelling an order restores stock exactly once', function () {
    [$order, $product] = midtransOrder([], stock: 10, quantity: 2);
    $product->update(['stock' => 8]);

    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'cancelled',
            'payment_status' => $order->payment_status,
        ])
        ->assertSessionHas('success');

    expect($product->fresh()->stock)->toBe(10);

    // Re-submitting cancelled must not restore stock again (double restore bug).
    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'cancelled',
            'payment_status' => $order->payment_status,
        ])
        ->assertSessionHas('success');

    expect($product->fresh()->stock)->toBe(10);
});

test('re-activating a cancelled order re-deducts stock and cancelling again does not double it', function () {
    [$order, $product] = midtransOrder([], stock: 8, quantity: 2);
    $order->update(['status' => 'cancelled']);

    // Leave cancelled: stock must be re-deducted.
    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'processing',
            'payment_status' => 'paid',
        ])
        ->assertSessionHas('success');

    expect($product->fresh()->stock)->toBe(6);

    // Cancel again: stock restored once.
    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'cancelled',
            'payment_status' => 'paid',
        ])
        ->assertSessionHas('error'); // paid + cancelled is rejected

    // Use unpaid so the cancel is accepted.
    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
        ])
        ->assertSessionHas('success');

    expect($product->fresh()->stock)->toBe(8);
});

test('stock never goes negative when re-activating a cancelled order', function () {
    [$order, $product] = midtransOrder([], stock: 0, quantity: 5);
    $order->update(['status' => 'cancelled']);

    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'processing',
            'payment_status' => 'unpaid',
        ])
        ->assertSessionHas('success');

    expect($product->fresh()->stock)->toBe(0);
});

test('paid order cannot be cancelled', function () {
    [$order] = midtransOrder();

    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'cancelled',
            'payment_status' => 'paid',
        ])
        ->assertSessionHas('error');

    expect($order->fresh()->status)->not->toBe('cancelled');
});

test('failed payment requires the order to be cancelled', function () {
    [$order, $product] = midtransOrder([], stock: 8, quantity: 2);

    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'processing',
            'payment_status' => 'failed',
        ])
        ->assertSessionHas('error');

    expect($order->fresh()->status)->toBe('pending');

    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'cancelled',
            'payment_status' => 'failed',
        ])
        ->assertSessionHas('success');

    expect($order->fresh()->status)->toBe('cancelled');
    expect($product->fresh()->stock)->toBe(10);
});

test('marking a pending order as paid moves it to processing', function () {
    [$order] = midtransOrder();

    $this->actingAs(admin())
        ->put(route('admin.orders.update', $order), [
            'status' => 'pending',
            'payment_status' => 'paid',
        ])
        ->assertSessionHas('success');

    expect($order->fresh()->status)->toBe('processing');
    expect($order->fresh()->payment_status)->toBe('paid');
});

test('customers cannot update orders', function () {
    [$order] = midtransOrder();

    $this->actingAs(customer())
        ->put(route('admin.orders.update', $order), [
            'status' => 'completed',
            'payment_status' => 'paid',
        ])
        ->assertRedirect(route('customer.dashboard'));

    expect($order->fresh()->status)->toBe('pending');
});

test('guests cannot update orders', function () {
    [$order] = midtransOrder();

    $this->put(route('admin.orders.update', $order), [
        'status' => 'completed',
        'payment_status' => 'paid',
    ])->assertRedirect('/login');
});
