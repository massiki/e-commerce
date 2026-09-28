<?php

use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;

beforeEach(function () {
    $this->fake = fakeMidtrans();
});

it('rejects webhook with invalid signature', function () {
    [$order] = midtransOrder();

    $this->postJson('/api/payment/notification', [
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => '101000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'tx-123',
        'signature_key' => 'invalid-signature',
    ])->assertStatus(403);

    expect($order->refresh()->payment_status)->toBe('unpaid')
        ->and(Payment::count())->toBe(0);
});

it('rejects webhook without required fields', function () {
    $this->postJson('/api/payment/notification', [])->assertStatus(400);
});

it('returns 502 when midtrans status is unreachable', function () {
    [$order] = midtransOrder();
    $this->fake->statusThrows = true;

    $this->postJson('/api/payment/notification', signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => '101000.00',
        'transaction_status' => 'settlement',
        'transaction_id' => 'tx-123',
    ]))->assertStatus(502);
});

it('returns 404 when transaction does not exist', function () {
    [$order] = midtransOrder();
    $this->fake->statusResponse = null;

    $this->postJson('/api/payment/notification', signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => '101000.00',
        'transaction_status' => 'settlement',
        'transaction_id' => 'tx-123',
    ]))->assertStatus(404);
});

it('marks order paid on settlement without touching stock', function () {
    [$order, $product] = midtransOrder();
    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'gross_amount' => '101000.00',
    ]);

    $this->postJson('/api/payment/notification', signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => '101000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'tx-123',
    ]))->assertOk()->assertJson(['message' => 'OK']);

    expect($order->refresh()->payment_status)->toBe('paid')
        ->and($order->status)->toBe('processing')
        ->and($product->refresh()->stock)->toBe(10)
        ->and(Payment::count())->toBe(1)
        ->and(Notification::where('type', 'payment_settlement')->count())->toBe(1);
});

it('restores stock only once for duplicated failure notifications', function () {
    [$order, $product] = midtransOrder();

    $payload = signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '201',
        'gross_amount' => '101000.00',
        'transaction_status' => 'deny',
        'fraud_status' => 'deny',
        'transaction_id' => 'tx-123',
    ]);

    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'transaction_status' => 'deny',
        'fraud_status' => 'deny',
    ]);

    $this->postJson('/api/payment/notification', $payload)->assertOk();
    $this->postJson('/api/payment/notification', $payload)->assertOk();

    expect($product->refresh()->stock)->toBe(12)
        ->and($order->refresh()->payment_status)->toBe('failed')
        ->and($order->status)->toBe('cancelled')
        ->and(Notification::where('type', 'payment_failed')->count())->toBe(1)
        ->and(Payment::count())->toBe(2);
});

it('sets payment status to challenge on capture with fraud challenge', function () {
    [$order] = midtransOrder();

    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'transaction_status' => 'capture',
        'fraud_status' => 'challenge',
    ]);

    $this->postJson('/api/payment/notification', signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => '101000.00',
        'transaction_status' => 'capture',
        'fraud_status' => 'challenge',
        'transaction_id' => 'tx-123',
    ]))->assertOk();

    expect($order->refresh()->payment_status)->toBe('challenge')
        ->and($order->status)->toBe('pending');
});

it('ignores settlement with mismatched gross amount', function () {
    [$order, $product] = midtransOrder();

    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'gross_amount' => '99000.00',
    ]);

    $this->postJson('/api/payment/notification', signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => '99000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'tx-123',
    ]))->assertOk();

    expect($order->refresh()->payment_status)->toBe('unpaid')
        ->and($order->status)->toBe('pending')
        ->and($product->refresh()->stock)->toBe(10)
        ->and(Payment::count())->toBe(1)
        ->and(Notification::where('type', 'payment_anomaly')->count())->toBe(1);
});

it('re-deducts stock when settlement arrives for a cancelled order', function () {
    [$order, $product] = midtransOrder();

    // Simulasikan order yang dibatalkan user (stok sudah dikembalikan).
    $product->update(['stock' => 12]);
    $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);

    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'gross_amount' => '101000.00',
    ]);

    $this->postJson('/api/payment/notification', signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => '101000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'tx-123',
    ]))->assertOk();

    $order->refresh();

    expect($order->payment_status)->toBe('paid')
        ->and($order->status)->toBe('processing')
        ->and($product->refresh()->stock)->toBe(10)
        ->and(Notification::where('type', 'payment_anomaly')->count())->toBe(1);
});

it('does not downgrade an already paid order on failure notification', function () {
    [$order, $product] = midtransOrder(['payment_status' => 'paid', 'status' => 'processing']);

    $this->fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'transaction_status' => 'cancel',
        'fraud_status' => null,
    ]);

    $this->postJson('/api/payment/notification', signNotification([
        'order_id' => $order->invoice_number,
        'status_code' => '407',
        'gross_amount' => '101000.00',
        'transaction_status' => 'cancel',
        'fraud_status' => null,
        'transaction_id' => 'tx-123',
    ]))->assertOk();

    expect($order->refresh()->payment_status)->toBe('paid')
        ->and($order->status)->toBe('processing')
        ->and($product->refresh()->stock)->toBe(10);
});
