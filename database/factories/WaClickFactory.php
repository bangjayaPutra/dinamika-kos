<?php

namespace Database\Factories;

use App\Models\RoomType;
use App\Models\WaClick;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WaClick>
 */
class WaClickFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_type_id' => null,
            'ip_address' => $this->faker->ipv4(),
            'created_at' => now(),
        ];
    }

    /**
     * Attribute the click to the given room type.
     */
    public function forRoomType(RoomType $roomType): static
    {
        return $this->state(fn (array $attributes): array => ['room_type_id' => $roomType->id]);
    }
}
