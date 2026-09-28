<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'name' => 'Admin',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'phone' => '081234567890',
        ]);

        User::firstOrCreate(['email' => 'customer@gmail.com'], [
            'name' => 'Customer',
            'password' => bcrypt('password'),
            'role_id' => $customerRole->id,
            'phone' => '081234567891',
        ]);

        // Data demo hanya dibuat sekali supaya seeder aman dijalankan berulang.
        if (Product::count() > 0) {
            return;
        }

        $brands = ['Nike', 'Adidas', 'Puma', 'Uniqlo', 'Zara'];
        foreach ($brands as $name) {
            Brand::create(['name' => $name, 'slug' => str()->slug($name)]);
        }

        $categories = ['Elektronik', 'Pakaian', 'Makanan', 'Minuman', 'Buku'];
        foreach ($categories as $name) {
            Category::create(['name' => $name, 'slug' => str()->slug($name)]);
        }

        Product::factory(50)->create();

        $products = Product::inRandomOrder()->take(30)->get();
        foreach ($products as $product) {
            Discount::factory()->create(['product_id' => $product->id]);
        }

        Review::factory(100)->create();
    }
}
