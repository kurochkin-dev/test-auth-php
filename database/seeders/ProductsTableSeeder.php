<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\ProductOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        // Создаем 50 товаров
        $products = Product::factory()->count(50)->create();

        // Создаем опции товаров
        $productOptions = ProductOption::factory()->count(10)->create();

        // Связываем товары с опциями
        $products->each(function ($product) use ($productOptions) {
            $options = $productOptions->random(rand(1, 5))->pluck('id');
            foreach ($options as $option) {
                DB::table('product_option_values')->insert([
                    'product_id' => $product->id,
                    'product_option_id' => $option,
                    'value' => 'Random Value', // Замените на случайное значение, если необходимо
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}

