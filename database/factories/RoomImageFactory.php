<?php

namespace Database\Factories;

use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomImage>
 */
class RoomImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_type_id' => RoomType::factory(),
            'path' => 'room-images/'.$this->faker->uuid().'.jpg',
            'is_cover' => false,
            'sort_order' => 0,
        ];
    }

    /**
     * Mark the image as the cover of its room type.
     */
    public function cover(): static
    {
        return $this->state(fn (array $attributes): array => ['is_cover' => true]);
    }
}
