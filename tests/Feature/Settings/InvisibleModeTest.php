<?php

namespace Tests\Feature\Settings;

use App\Enums\InviteStatus;
use App\Models\Invite;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvisibleModeTest extends TestCase
{
    use RefreshDatabase;

    // ─── Profile toggle ───────────────────────────────────────────────────────

    public function test_user_can_enable_invisible_mode(): void
    {
        $user = User::factory()->create(['is_invisible' => false]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'is_invisible' => true,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertTrue($user->fresh()->is_invisible);
    }

    public function test_user_can_disable_invisible_mode(): void
    {
        $user = User::factory()->create(['is_invisible' => true]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertFalse($user->fresh()->is_invisible);
    }

    // ─── Auto-revoke pending invites on enable ────────────────────────────────

    public function test_pending_invites_are_revoked_when_enabling_invisible(): void
    {
        $user = User::factory()->create(['is_invisible' => false]);
        $invite1 = Invite::factory()->create(['target_id' => $user->id, 'status' => InviteStatus::PENDING]);
        $invite2 = Invite::factory()->create(['target_id' => $user->id, 'status' => InviteStatus::PENDING]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'is_invisible' => true,
            ]);

        $this->assertEquals(InviteStatus::REVOKED, $invite1->fresh()->status);
        $this->assertEquals(InviteStatus::REVOKED, $invite2->fresh()->status);
    }

    public function test_non_pending_invites_are_not_changed_when_enabling_invisible(): void
    {
        $user = User::factory()->create(['is_invisible' => false]);
        $accepted = Invite::factory()->create(['target_id' => $user->id, 'status' => InviteStatus::ACCEPTED]);
        $revoked = Invite::factory()->create(['target_id' => $user->id, 'status' => InviteStatus::REVOKED]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'is_invisible' => true,
            ]);

        $this->assertEquals(InviteStatus::ACCEPTED, $accepted->fresh()->status);
        $this->assertEquals(InviteStatus::REVOKED, $revoked->fresh()->status);
    }

    public function test_pending_invites_are_not_revoked_when_already_invisible(): void
    {
        $user = User::factory()->create(['is_invisible' => true]);
        $invite = Invite::factory()->create(['target_id' => $user->id, 'status' => InviteStatus::PENDING]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'is_invisible' => true,
            ]);

        $this->assertEquals(InviteStatus::PENDING, $invite->fresh()->status);
    }

    // ─── Invite blocking ──────────────────────────────────────────────────────

    public function test_non_admin_cannot_invite_invisible_user(): void
    {
        $author = User::factory()->create(['is_admin' => false]);
        $invisible = User::factory()->create(['is_invisible' => true]);
        $reservation = Reservation::factory()->create();

        $this->actingAs($author)
            ->call('GET', '/api/V1/invite/create', [
                'author_id' => $author->id,
                'target_id' => $invisible->id,
                'reservation_id' => $reservation->id,
            ])
            ->assertUnprocessable();
    }

    public function test_admin_can_invite_invisible_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $invisible = User::factory()->create(['is_invisible' => true]);
        $reservation = Reservation::factory()->create();

        $this->actingAs($admin)
            ->call('GET', '/api/V1/invite/create', [
                'author_id' => $admin->id,
                'target_id' => $invisible->id,
                'reservation_id' => $reservation->id,
            ])
            ->assertOk();
    }

    public function test_anyone_can_invite_visible_user(): void
    {
        $author = User::factory()->create(['is_admin' => false]);
        $visible = User::factory()->create(['is_invisible' => false]);
        $reservation = Reservation::factory()->create();

        $this->actingAs($author)
            ->call('GET', '/api/V1/invite/create', [
                'author_id' => $author->id,
                'target_id' => $visible->id,
                'reservation_id' => $reservation->id,
            ])
            ->assertOk();
    }

    // ─── User list filtering ──────────────────────────────────────────────────

    public function test_invisible_user_excluded_from_booking_form_for_non_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $invisible = User::factory()->create(['is_invisible' => true]);
        User::factory()->create(['is_invisible' => false]);

        $response = $this->actingAs($user)
            ->get(route('reservations.index'));

        $bookingFormData = $response->original->getData()['page']['props']['bookingFormData'];
        $ids = collect($bookingFormData['users'])->pluck('id');

        $this->assertFalse($ids->contains($invisible->id));
    }

    public function test_admin_sees_invisible_users_in_booking_form_data(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $invisible = User::factory()->create(['is_invisible' => true]);

        $response = $this->actingAs($admin)
            ->get(route('reservations.index'));

        $bookingFormData = $response->original->getData()['page']['props']['bookingFormData'];
        $ids = collect($bookingFormData['users'])->pluck('id');

        $this->assertTrue($ids->contains($invisible->id));
    }

    public function test_non_admin_does_not_see_invisible_users_in_booking_form_data(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $invisible = User::factory()->create(['is_invisible' => true]);

        $response = $this->actingAs($user)
            ->get(route('reservations.index'));

        $bookingFormData = $response->original->getData()['page']['props']['bookingFormData'];
        $ids = collect($bookingFormData['users'])->pluck('id');

        $this->assertFalse($ids->contains($invisible->id));
    }

    // ─── BookingRequest removes invisible ─────────────────────────────────────

    public function test_creating_booking_request_removes_invisible_status(): void
    {
        $user = User::factory()->create(['is_invisible' => true, 'contacts' => 'test']);

        $this->actingAs($user)
            ->post(route('booking-requests.store'), [
                'date' => '2026-01-01',
            ])
            ->assertRedirect(route('reservations.index'));

        $this->assertFalse($user->fresh()->is_invisible);
    }

    public function test_creating_booking_request_does_not_change_visible_user(): void
    {
        $user = User::factory()->create(['is_invisible' => false, 'contacts' => 'test']);

        $this->actingAs($user)
            ->post(route('booking-requests.store'), [
                'date' => '2026-01-01',
            ]);

        $this->assertFalse($user->fresh()->is_invisible);
    }
}
