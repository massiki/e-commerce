<?php

use App\Models\Notification;

function makeStaleOrder(array $attributes = []): array
{
    [$order, $product] = midtransOrder($attributes);
    $order->forceFill(['created_at' => now()->subMinutes(15)])->save();

    return [$order, $product];
}

it('expires stale unpaid midtrans orders and restores stock', function () {
    [$order, $product] = makeStaleOrder();
    $fake = fakeMidtrans();

    $fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'transaction_status' => 'pending',
        'fraud_status' => null,
    ]);
    $fake->expireResponse = (object) [
        'order_id' => $order->invoice_number,
        'transaction_id' => 'tx-123',
        'status_code' => '407',
        'transaction_status' => 'expire',
        'fraud_status' => null,
        'gross_amount' => '101000.00',
    ];

    $this->artisan('orders:reconcile')->assertExitCode(0);

    expect($fake->lastExpired)->toBe($order->invoice_number)
        ->and($order->refresh()->payment_status)->toBe('failed')
        ->and($order->status)->toBe('cancelled')
        ->and($product->refresh()->stock)->toBe(12)
        ->and(Notification::where('type', 'payment_expired')->count())->toBe(1);
});

it('ignores orders that are not yet stale', function () {
    [$order, $product] = midtransOrder();
    $fake = fakeMidtrans();
    $fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'transaction_status' => 'pending',
    ]);

    $this->artisan('orders:reconcile')->assertExitCode(0);

    expect($fake->lastExpired)->toBeNull()
        ->and($order->refresh()->payment_status)->toBe('unpaid')
        ->and($order->status)->toBe('pending');
});

it('applies settlement reported by midtrans during reconcile', function () {
    [$order, $product] = makeStaleOrder();
    $fake = fakeMidtrans();
    $fake->statusResponse = midtransStatus([
        'order_id' => $order->invoice_number,
        'gross_amount' => '101000.00',
    ]);

    $this->artisan('orders:reconcile')->assertExitCode(0);

    expect($order->refresh()->payment_status)->toBe('paid')
        ->and($order->status)->toBe('processing')
        ->and($product->refresh()->stock)->toBe(10);
});

it('locally expires orders unknown to midtrans', function () {
    [$order, $product] = makeStaleOrder();
    $fake = fakeMidtrans();
    $fake->statusResponse = null;

    $this->artisan('orders:reconcile')->assertExitCode(0);

    expect($order->refresh()->payment_status)->toBe('failed')
        ->and($order->status)->toBe('cancelled')
        ->and($product->refresh()->stock)->toBe(12);
});

it('skips reconcile when midtrans is unreachable', function () {
    [$order, $product] = makeStaleOrder();
    $fake = fakeMidtrans();
    $fake->statusThrows = true;

    $this->artisan('orders:reconcile')->assertExitCode(0);

    expect($order->refresh()->payment_status)->toBe('unpaid')
        ->and($order->status)->toBe('pending')
        ->and($product->refresh()->stock)->toBe(10);
});
