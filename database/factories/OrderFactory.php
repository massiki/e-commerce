<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(100000, 500000);
        $shipping = $this->faker->numberBetween(0, 20000);

        return [
            'user_id' => User::factory(),
            'invoice_number' => 'INV-'.strtoupper($this->faker->unique()->bothify('?????-#####')),
            'recipient_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'province' => $this->faker->state(),
            'city' => $this->faker->city(),
            'district' => $this->faker->streetName(),
            'postal_code' => $this->faker->postcode(),
            'full_address' => $this->faker->address(),
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'total' => $subtotal + $shipping,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ];
    }
}
