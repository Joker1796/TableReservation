<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    const array ARGUMENTS = [
        'title' => 'Test event title',
        'description' => 'Test event description',
        'starts_at' => '2027-01-01 18:00:00',
        'ends_at' => '2027-01-01 22:00:00',
    ];

    const array UPDATED_ARGUMENTS = [
        'title' => 'Updated event title',
        'description' => 'Updated event description',
        'starts_at' => '2027-02-01 18:00:00',
        'ends_at' => null,
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 month', '+3 months');

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'starts_at' => $start,
            'ends_at' => fake()->optional()->dateTimeBetween($start, (clone $start)->modify('+1 day')),
            'author_id' => User::factory(),
        ];
    }
}
