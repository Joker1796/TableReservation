<?php

namespace Database\Seeders;

use App\Models\ReservationRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create();
        $reservationRequest = ReservationRequest::factory()->make();

        $reservationRequest->user()->associate($user);
    }
}
