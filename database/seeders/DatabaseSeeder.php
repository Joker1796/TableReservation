<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Reservation::factory()
            ->has(User::factory(), 'users')
            ->count(5)
            ->create();

        Table::factory()
            ->has(Reservation::factory()->count(3))
            ->count(4)
            ->create();

        User::factory()
            ->has(Reservation::factory()->count(5))
            ->create();
    }
}
