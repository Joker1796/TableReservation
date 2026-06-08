<?php

namespace Tests\Feature\Web;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    // --- access control ---

    public function test_guest_is_redirected_from_events(): void
    {
        $this->get(route('events'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_events_page(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('events'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('events/Index')
                ->has('upcomingEvents')
                ->has('recentEvents')
            );
    }

    // --- upcoming / recent separation ---

    public function test_upcoming_events_appear_in_upcoming_prop(): void
    {
        $this->actingAs(User::factory()->create());
        Event::factory()->create(['starts_at' => now()->addDay()]);

        $this->get(route('events'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('upcomingEvents', 1)
                ->has('recentEvents', 0)
            );
    }

    public function test_past_events_appear_in_recent_prop(): void
    {
        $this->actingAs(User::factory()->create());
        Event::factory()->create(['starts_at' => now()->subDay()]);

        $this->get(route('events'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('upcomingEvents', 0)
                ->has('recentEvents', 1)
            );
    }

    public function test_upcoming_and_recent_are_split_correctly(): void
    {
        $this->actingAs(User::factory()->create());
        Event::factory()->count(3)->create(['starts_at' => now()->addDays(2)]);
        Event::factory()->count(2)->create(['starts_at' => now()->subDays(2)]);

        $this->get(route('events'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('upcomingEvents', 3)
                ->has('recentEvents', 2)
            );
    }

    // --- limits ---

    public function test_events_page_returns_up_to_twenty_upcoming_events(): void
    {
        $this->actingAs(User::factory()->create());
        Event::factory()->count(25)->create(['starts_at' => now()->addDay()]);

        $this->get(route('events'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('upcomingEvents', 20)
            );
    }

    public function test_events_page_returns_up_to_ten_recent_events(): void
    {
        $this->actingAs(User::factory()->create());
        Event::factory()->count(15)->create(['starts_at' => now()->subDay()]);

        $this->get(route('events'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('recentEvents', 10)
            );
    }

    // --- ordering ---

    public function test_upcoming_events_ordered_by_start_time_ascending(): void
    {
        $this->actingAs(User::factory()->create());
        $later = Event::factory()->create(['starts_at' => now()->addDays(5)]);
        $sooner = Event::factory()->create(['starts_at' => now()->addDay()]);

        $this->get(route('events'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('upcomingEvents.0.id', $sooner->id)
                ->where('upcomingEvents.1.id', $later->id)
            );
    }

    public function test_recent_events_ordered_by_start_time_descending(): void
    {
        $this->actingAs(User::factory()->create());
        $older = Event::factory()->create(['starts_at' => now()->subDays(5)]);
        $newer = Event::factory()->create(['starts_at' => now()->subDay()]);

        $this->get(route('events'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('recentEvents.0.id', $newer->id)
                ->where('recentEvents.1.id', $older->id)
            );
    }

    // --- event data structure ---

    public function test_upcoming_events_include_participants(): void
    {
        $this->actingAs(User::factory()->create());
        $event = Event::factory()->create(['starts_at' => now()->addDay()]);
        $participant = User::factory()->create();
        $event->participants()->attach($participant->id);

        $this->get(route('events'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('upcomingEvents.0.participants', 1)
            );
    }
}
