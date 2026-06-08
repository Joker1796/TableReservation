<?php

namespace Tests\Feature\Web;

use App\Mail\NewEventRegistrationMail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    // --- guest redirects ---

    public function test_guest_cannot_register_for_event(): void
    {
        $event = Event::factory()->create();
        $this->post(route('events.register', $event->id))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_unregister_from_event(): void
    {
        $event = Event::factory()->create();
        $this->delete(route('events.unregister', $event->id))
            ->assertRedirect(route('login'));
    }

    // --- no contacts redirect ---

    public function test_user_without_contacts_cannot_register_for_event(): void
    {
        $user = User::factory()->create(['phone' => null, 'contacts' => null]);
        $event = Event::factory()->create(['starts_at' => now()->addDay()]);

        $this->actingAs($user)
            ->post(route('events.register', $event->id))
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseEmpty('event_user');
    }

    // --- registration ---

    public function test_user_can_register_for_event(): void
    {
        Mail::fake();
        $user = User::factory()->create(['phone' => '79001234567']);
        $event = Event::factory()->create(['starts_at' => now()->addDay()]);

        $this->actingAs($user)
            ->post(route('events.register', $event->id));

        $this->assertDatabaseHas('event_user', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_registration_queues_email_to_admins(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['phone' => '79001234567']);
        $event = Event::factory()->create(['starts_at' => now()->addDay()]);

        $this->actingAs($user)
            ->post(route('events.register', $event->id));

        Mail::assertQueued(NewEventRegistrationMail::class, fn ($mail) => $mail->hasTo($admin->email)
        );
    }

    public function test_duplicate_registration_is_ignored(): void
    {
        Mail::fake();
        $user = User::factory()->create(['phone' => '79001234567']);
        $event = Event::factory()->create(['starts_at' => now()->addDay()]);

        $this->actingAs($user)->post(route('events.register', $event->id));
        $this->actingAs($user)->post(route('events.register', $event->id));

        $this->assertDatabaseCount('event_user', 1);
    }

    public function test_cannot_register_for_past_event(): void
    {
        $user = User::factory()->create(['phone' => '79001234567']);
        $event = Event::factory()->create(['starts_at' => now()->subDay()]);

        $this->actingAs($user)
            ->post(route('events.register', $event->id))
            ->assertStatus(422);

        $this->assertDatabaseEmpty('event_user');
    }

    // --- unregistration ---

    public function test_user_can_unregister_from_event(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['starts_at' => now()->addDay()]);
        $event->participants()->attach($user->id);

        $this->actingAs($user)
            ->delete(route('events.unregister', $event->id));

        $this->assertDatabaseMissing('event_user', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_unregister_from_past_event(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['starts_at' => now()->subDay()]);
        $event->participants()->attach($user->id);

        $this->actingAs($user)
            ->delete(route('events.unregister', $event->id))
            ->assertStatus(422);

        $this->assertDatabaseHas('event_user', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
    }

    // --- markSeen ---

    public function test_non_admin_cannot_mark_registrations_seen(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.events.registrations.seen'))
            ->assertForbidden();
    }

    public function test_admin_can_mark_registrations_seen(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $event->participants()->attach($user->id, ['seen_by_admin' => false]);

        $this->actingAs($admin)
            ->post(route('admin.events.registrations.seen'))
            ->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('event_user', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'seen_by_admin' => true,
        ]);
    }
}
