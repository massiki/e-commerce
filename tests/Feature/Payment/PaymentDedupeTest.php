<?php

use App\Models\Notification;
use App\Models\Payment;

beforeEach(function () {
    $this->fake = fakeMidtrans();
});

test('duplicate settlement notifications create a single payment row', function () {
    [$order] = midtransOrder();
    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'gross_amount' => '101000.00',
    ]);

    $payload = signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => '101000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'tx-123',
    ]);

    $this->postJson('/api/payment/notification', $payload)->assertOk();
    $this->postJson('/api/payment/notification', $payload)->assertOk();

    expect(Payment::count())->toBe(1)
        ->and($order->refresh()->payment_status)->toBe('paid')
        ->and(Notification::where('type', 'payment_settlement')->count())->toBe(1);
});

test('mismatched amount logs and notifies only once per order', function () {
    [$order] = midtransOrder();
    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'gross_amount' => '999.00',
    ]);

    $payload = signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => '999.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'tx-123',
    ]);

    $this->postJson('/api/payment/notification', $payload)->assertOk();
    $this->postJson('/api/payment/notification', $payload)->assertOk();

    expect(Notification::where('type', 'payment_anomaly')->count())->toBe(1)
        ->and(Payment::count())->toBe(1)
        ->and($order->refresh()->payment_status)->toBe('unpaid');
});

test('reconcile skips challenged orders instead of polling them forever', function () {
    [$order] = midtransOrder([
        'payment_status' => 'challenge',
        'status' => 'pending',
        'created_at' => now()->subHours(2),
    ]);

    $this->artisan('orders:reconcile')->assertSuccessful();

    expect($this->fake->statusCalls)->toBe(0);
});

test('reconcile still polls stale unpaid orders', function () {
    [$order] = midtransOrder([
        'payment_status' => 'unpaid',
        'status' => 'pending',
        'created_at' => now()->subHours(2),
    ]);
    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'transaction_status' => 'pending',
    ]);
    $this->fake->expireResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'transaction_status' => 'expire',
    ]);

    $this->artisan('orders:reconcile')->assertSuccessful();

    expect($this->fake->statusCalls)->toBe(1)
        ->and($order->refresh()->payment_status)->toBe('failed');
});
