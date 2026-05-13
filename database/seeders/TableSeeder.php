<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Table::factory()
            ->has(Reservation::factory()->count(3))
            ->count(4)
            ->create();
    }
}
