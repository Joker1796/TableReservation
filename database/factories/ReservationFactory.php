<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    const array ARGUMENTS = [
        'comment' => 'test comment',
        'date' => '01.01.2026 00:00:00',
    ];

    const array UPDATED_ARGUMENTS = [
        'comment' => 'test comment updated',
        'date' => '02.01.2026 10:00:00',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment' => fake()->text(),
            'date' => $this->faker->date(),
        ];
    }
}
