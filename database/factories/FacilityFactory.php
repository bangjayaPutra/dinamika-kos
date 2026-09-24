<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucwords(fake()->unique()->words(2, true)),
            'icon' => null,
            'description' => fake()->sentence(),
            'scope' => fake()->randomElement(['kamar', 'umum']),
        ];
    }

    /**
     * Indicate that the facility applies to a room.
     */
    public function kamar(): static
    {
        return $this->state(fn (array $attributes): array => [
            'scope' => 'kamar',
        ]);
    }

    /**
     * Indicate that the facility is a shared (umum) facility.
     */
    public function umum(): static
    {
        return $this->state(fn (array $attributes): array => [
            'scope' => 'umum',
        ]);
    }
}
