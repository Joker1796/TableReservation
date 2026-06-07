<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        return $admin;
    }

    // --- guest redirects ---

    public function test_guests_are_redirected_from_index(): void
    {
        $this->get(route('admin.events.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_create(): void
    {
        $this->get(route('admin.events.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_store(): void
    {
        $this->post(route('admin.events.store'), Event::factory()::ARGUMENTS)
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_destroy(): void
    {
        $event = Event::factory()->create();
        $this->delete(route('admin.events.destroy', $event->id))
            ->assertRedirect(route('login'));
    }

    // --- non-admin forbidden ---

    public function test_non_admin_is_forbidden_from_index(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.events.index'))
            ->assertForbidden();
    }

    public function test_non_admin_is_forbidden_from_store(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post(route('admin.events.store'), Event::factory()::ARGUMENTS)
            ->assertForbidden();
    }

    public function test_non_admin_is_forbidden_from_destroy(): void
    {
        $event = Event::factory()->create();
        $this->actingAs(User::factory()->create());
        $this->delete(route('admin.events.destroy', $event->id))
            ->assertForbidden();
    }

    // --- admin happy path ---

    public function test_admin_can_view_events_index(): void
    {
        $this->admin();
        $this->get(route('admin.events.index'))
            ->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->admin();
        $this->get(route('admin.events.create'))
            ->assertOk();
    }

    public function test_admin_can_create_event(): void
    {
        $this->admin();

        $this->post(route('admin.events.store'), Event::factory()::ARGUMENTS)
            ->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('events', ['title' => 'Test event title']);
    }

    public function test_admin_can_view_edit_form(): void
    {
        $this->admin();
        $event = Event::factory()->create();

        $this->get(route('admin.events.edit', $event->id))
            ->assertOk();
    }

    public function test_admin_can_update_event(): void
    {
        $this->admin();
        $event = Event::factory()->create();

        $this->put(route('admin.events.update', $event->id), Event::factory()::UPDATED_ARGUMENTS)
            ->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated event title',
        ]);
    }

    public function test_admin_can_delete_event(): void
    {
        $this->admin();
        $event = Event::factory()->create();

        $this->delete(route('admin.events.destroy', $event->id))
            ->assertRedirect(route('admin.events.index'));

        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }

    // --- validation ---

    public function test_event_not_created_without_title(): void
    {
        $this->admin();

        $data = Event::factory()::ARGUMENTS;
        unset($data['title']);

        $this->post(route('admin.events.store'), $data)
            ->assertSessionHasErrors(['title']);
    }

    public function test_event_not_created_without_starts_at(): void
    {
        $this->admin();

        $data = Event::factory()::ARGUMENTS;
        unset($data['starts_at']);

        $this->post(route('admin.events.store'), $data)
            ->assertSessionHasErrors(['starts_at']);
    }
}
