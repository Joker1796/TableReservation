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
    const array ARGUMENTS = [
        'title' => 'Test post title',
        'content' => '<p>Test content</p>',
        'published_at' => '2026-01-01 00:00:00',
    ];

    const array UPDATED_ARGUMENTS = [
        'title' => 'Updated post title',
        'content' => '<p>Updated content</p>',
        'published_at' => '2026-02-01 00:00:00',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'author_id' => User::factory(),
        ];
    }
}
