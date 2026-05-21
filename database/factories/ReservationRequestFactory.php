<?php

namespace Database\Factories;

use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReservationRequest>
 */
class ReservationRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment' => fake()->text(),
            'date' => $this->faker->date(),
            'hours' => $this->faker->numberBetween(1, 12),
            'status' => $this->faker->numberBetween(1, 4),
            'author_id' => User::factory(),
            'table_id' => Table::factory(),
        ];
    }
}
