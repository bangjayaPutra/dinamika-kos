<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->unique()->slug(),
            'excerpt' => $this->faker->paragraph(),
            'body' => $this->faker->paragraphs(3, true),
            'cover_path' => $this->faker->imageUrl(),
            'is_published' => $this->faker->boolean(),
            'published_at' => $this->faker->dateTime(),
            'author_id' => User::factory(),
        ];
    }

    /**
     * Mark the post as published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes): array => ['is_published' => true, 'published_at' => now()]);
    }
}
