<?php

namespace Database\Factories;

use App\Models\CarouselItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarouselItem>
 */
class CarouselItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->sentence(),
            'image_path' => 'carousel/'.$this->faker->uuid().'.jpg',
            'link_url' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    /**
     * Mark the item as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => false]);
    }
}
