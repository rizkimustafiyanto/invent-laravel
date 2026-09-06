<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('ITM#####'),
            'name' => fake()->words(rand(1, 3), true),
            'price' => fake()->numberBetween(5000, 500000),
            'stock' => fake()->numberBetween(25, 250),
            'category_id' => Category::query()->inRandomOrder()->value('id'),
            'image_path' => null,
        ];
    }
}
