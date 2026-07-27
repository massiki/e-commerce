<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $price = $this->faker->numberBetween(10000, 100000);

        return [
            'order_id' => Order::factory(),
            'product_name' => $this->faker->words(3, true),
            'price' => $price,
            'quantity' => $this->faker->numberBetween(1, 3),
            'category_name' => $this->faker->word(),
            'brand_name' => $this->faker->word(),
        ];
    }
}
