<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\ProductProperty;
use Faker\Generator as Faker;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductProperty>
 */
class ProductPropertyFactory extends Factory
{

    protected $model = ProductProperty::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => function () {
                return Product::factory()->create()->id;
            },
            'property_name' => $this->faker->word,
            'property_value' => $this->faker->randomElement(['red', 'white', 'blue', 'green', '1000', '1250', '1500']),
        ];
    }
}
