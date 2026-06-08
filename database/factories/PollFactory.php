<?php

namespace Database\Factories;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poll>
 */
class PollFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => fake()->sentence().'?',
            'description' => fake()->optional()->sentence(),
            'allow_multiple' => false,
            'author_id' => User::factory(),
            'closes_at' => null,
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function withOptions(int $count = 3): static
    {
        return $this->afterCreating(function (Poll $poll) use ($count): void {
            for ($i = 0; $i < $count; $i++) {
                $poll->options()->create([
                    'text' => fake()->words(3, true),
                    'sort_order' => $i,
                ]);
            }
        });
    }

    public function multipleChoice(): static
    {
        return $this->state(['allow_multiple' => true]);
    }

    public function closed(): static
    {
        return $this->state(['closes_at' => now()->subHour()]);
    }

    public function unpublished(): static
    {
        return $this->state(['published_at' => null]);
    }
}
