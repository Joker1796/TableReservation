<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventSuggestionTest extends TestCase
{
    use RefreshDatabase;

    private function suggestion(array $attrs = []): Event
    {
        return Event::factory()->create(array_merge([
            'is_suggestion' => true,
            'starts_at' => null,
        ], $attrs));
    }

    // --- access control ---

    public function test_guest_cannot_approve(): void
    {
        $event = $this->suggestion();

        $this->put(route('admin.events.approve', $event->id))
            ->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_approve(): void
    {
        $this->actingAs(User::factory()->create());
        $event = $this->suggestion();

        $this->put(route('admin.events.approve', $event->id))
            ->assertForbidden();
    }

    public function test_editor_cannot_approve(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));
        $event = $this->suggestion();

        $this->put(route('admin.events.approve', $event->id))
            ->assertForbidden();
    }

    // --- approve ---

    public function test_admin_can_approve_suggestion(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $event = $this->suggestion();

        $this->put(route('admin.events.approve', $event->id))
            ->assertRedirect(route('admin.events.index'));

        $event->refresh();
        $this->assertFalse($event->is_suggestion);
    }

    public function test_approve_sets_starts_at_when_null(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $event = $this->suggestion(['starts_at' => null]);

        $this->put(route('admin.events.approve', $event->id));

        $event->refresh();
        $this->assertNotNull($event->starts_at);
    }

    public function test_approve_preserves_existing_starts_at(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $startsAt = now()->addDays(7);
        $event = $this->suggestion(['starts_at' => $startsAt]);

        $this->put(route('admin.events.approve', $event->id));

        $event->refresh();
        $this->assertTrue($event->starts_at->isSameDay($startsAt));
    }

    public function test_cannot_approve_non_suggestion_event(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $event = Event::factory()->create(['is_suggestion' => false]);

        $this->put(route('admin.events.approve', $event->id))
            ->assertNotFound();
    }

    // --- pending suggestions in shared props ---

    public function test_pending_suggestions_shared_for_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);
        $this->suggestion();

        $this->get(route('feed'))
            ->assertInertia(fn ($page) => $page->has('pendingEventSuggestions', 1));
    }

    public function test_approved_suggestion_not_in_pending_props(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);
        $this->suggestion(['is_suggestion' => false]);

        $this->get(route('feed'))
            ->assertInertia(fn ($page) => $page->has('pendingEventSuggestions', 0));
    }

    public function test_pending_suggestions_not_shared_for_regular_user(): void
    {
        $this->actingAs(User::factory()->create());
        $this->suggestion();

        $this->get(route('feed'))
            ->assertInertia(fn ($page) => $page->where('pendingEventSuggestions', []));
    }

    // --- admin index shows suggestions ---

    public function test_admin_index_includes_suggestions(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->suggestion(['title' => 'Предложенное событие']);

        $this->get(route('admin.events.index'))
            ->assertInertia(fn ($page) => $page
                ->has('events.data', 1)
                ->where('events.data.0.is_suggestion', true)
            );
    }
}
