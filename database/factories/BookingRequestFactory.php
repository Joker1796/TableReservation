<?php

namespace Database\Factories;

use App\Models\BookingRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingRequest>
 */
class BookingRequestFactory extends Factory
{
    const array ARGUMENTS = [
        'comment' => 'test comment',
        'date' => '01.01.2026 00:00:00',
        'status' => 0,
    ];

    const array UPDATED_ARGUMENTS = [
        'comment' => 'test comment updated',
        'date' => '2026-01-02T10:00:00.000000Z',
        'status' => 1,
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment' => fake()->text(),
            'date' => $this->faker->date(),
            'status' => $this->faker->numberBetween(0, 2),
            'author_id' => User::factory(),
            'table_id' => Table::factory(),
        ];
    }
}
