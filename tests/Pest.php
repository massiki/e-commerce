<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakeMidtransService;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, the "Tests\TestCase" class that is defined here is automatically
| applied as the base class. The "pest()" function may be used to override this behavior.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function fakeMidtrans(): FakeMidtransService
{
    $fake = new FakeMidtransService;
    app()->instance(MidtransService::class, $fake);

    return $fake;
}

function customer(): User
{
    $role = Role::firstOrCreate(['name' => 'customer']);

    return User::factory()->create(['role_id' => $role->id]);
}

/**
 * Buat order Midtrans beserta product dan item-nya.
 *
 * @return array{0: Order, 1: Product}
 */
function midtransOrder(array $attributes = [], int $stock = 10, int $quantity = 2): array
{
    $product = Product::factory()->create(['stock' => $stock, 'price' => 50000]);

    $order = Order::factory()->create(array_merge([
        'user_id' => customer()->id,
        'payment_method' => 'midtrans',
        'payment_status' => 'unpaid',
        'status' => 'pending',
        'subtotal' => 100000,
        'total' => 101000,
    ], $attributes));

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => $quantity,
    ]);

    return [$order, $product];
}

function midtransStatus(array $overrides = []): object
{
    return (object) array_merge([
        'transaction_id' => 'tx-123',
        'order_id' => '',
        'status_code' => '200',
        'payment_type' => 'qris',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'gross_amount' => '101000.00',
        'signature_key' => '',
        'transaction_time' => now()->toDateTimeString(),
    ], $overrides);
}

function signNotification(array $payload): array
{
    $payload['signature_key'] = hash(
        'sha512',
        $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key')
    );

    return $payload;
}
