<?php

namespace Database\Seeders;

use App\Models\Invite;
use Illuminate\Database\Seeder;

class InviteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Invite::factory()
            ->count(10)
            ->create();
    }
}
