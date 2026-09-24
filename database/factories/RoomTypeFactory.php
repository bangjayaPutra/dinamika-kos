<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RoomType>
 */
class RoomTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucwords(fake()->unique()->words(2, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name.' '.fake()->unique()->randomNumber(5)),
            'description' => fake()->sentence(),
            'price_monthly' => fake()->numberBetween(5, 50) * 100000,
            'size_label' => fake()->randomElement(['3x3 m', '3x4 m', '4x4 m']),
            'capacity' => fake()->numberBetween(1, 2),
            'stock_total' => fake()->numberBetween(0, 20),
            'is_available' => true,
            'sort_order' => 0,
        ];
    }
}
