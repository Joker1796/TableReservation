<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_admin', true)->firstOrFail();

        // 3 upcoming events
        Event::factory()->count(3)->create([
            'author_id' => $admin->id,
            'starts_at' => fake()->dateTimeBetween('+1 day', '+3 months'),
            'ends_at' => null,
        ]);

        // 2 past events
        Event::factory()->count(2)->create([
            'author_id' => $admin->id,
            'starts_at' => fake()->dateTimeBetween('-3 months', '-1 day'),
            'ends_at' => null,
        ]);
    }
}
