<?php

namespace Database\Seeders;

use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create();
        $bookingRequest = BookingRequest::factory()->make();

        $bookingRequest->author()->associate($user);
    }
}
