<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sku' => 'SKU-' . strtoupper(fake()->unique()->bothify('??###')),
            'category' => fake()->randomElement(['Elektronik', 'Furniture', 'ATK', 'Perkakas']),
            'stock' => fake()->numberBetween(1, 100),
            'price' => fake()->randomFloat(2, 10000, 5000000),
            'description' => fake()->sentence(),
        ];
    }
}
