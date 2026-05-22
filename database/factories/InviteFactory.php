<?php

namespace Database\Factories;

use App\Enums\InviteStatus;
use App\Models\Invite;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invite>
 */
class InviteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(InviteStatus::cases()),
            'author_id' => User::factory(),
            'target_id' => User::factory(),
            'reservation_id' => Reservation::factory(),
        ];
    }
}
