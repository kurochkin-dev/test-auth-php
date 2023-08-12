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
        $products = Product::factory()->count(50)->create();

        $productOptions = ProductOption::factory()->count(10)->create();

        $products->each(function ($product) use ($productOptions) {
            $options = $productOptions->random(rand(1, 5))->pluck('id');
            foreach ($options as $optionName) {
                DB::table('product_options')->insert([
                    'product_id' => $product->id,
                    'name' => $optionName,
                    'value' => 'Random Value',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}

