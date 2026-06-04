<?php

namespace Tests\Feature;

use App\Enums\BookingRequestStatus;
use App\Enums\InviteStatus;
use App\Enums\TableStatus;
use App\Models\BookingRequest;
use App\Models\Invite;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_booking_form_data_is_shared_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Table::factory()->create(['status' => TableStatus::READY]);
        User::factory()->create();

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('bookingFormData.tables', 1)
                ->has('bookingFormData.users', 1)
            );
    }

    public function test_booking_form_data_only_includes_ready_tables(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Table::factory()->create(['status' => TableStatus::READY]);
        Table::factory()->create(['status' => TableStatus::NOT_READY]);

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('bookingFormData.tables', 1)
            );
    }

    public function test_booking_form_data_excludes_current_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('bookingFormData.users.0.id', $other->id)
                ->has('bookingFormData.users', 1)
            );
    }

    public function test_pending_invites_are_shared_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Invite::factory()->create([
            'target_id' => $user->id,
            'status' => InviteStatus::PENDING,
        ]);

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('pendingInvites', 1)
            );
    }

    public function test_pending_invites_only_includes_pending_status(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Invite::factory()->create(['target_id' => $user->id, 'status' => InviteStatus::PENDING]);
        Invite::factory()->create(['target_id' => $user->id, 'status' => InviteStatus::ACCEPTED]);
        Invite::factory()->create(['target_id' => $user->id, 'status' => InviteStatus::REVOKED]);

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('pendingInvites', 1)
            );
    }

    public function test_pending_invites_only_includes_current_users_invites(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user);

        Invite::factory()->create(['target_id' => $user->id,  'status' => InviteStatus::PENDING]);
        Invite::factory()->create(['target_id' => $other->id, 'status' => InviteStatus::PENDING]);

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('pendingInvites', 1)
            );
    }

    public function test_pending_invites_is_empty_array_for_guests(): void
    {
        $this->get('/')
            ->assertInertia(fn (Assert $page) => $page
                ->where('pendingInvites', [])
            );
    }

    // --- pending booking requests (admin notifications) ---

    public function test_pending_booking_requests_are_shared_with_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        BookingRequest::factory()->create(['status' => BookingRequestStatus::PENDING]);

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('pendingBookingRequests', 1)
            );
    }

    public function test_pending_booking_requests_only_includes_pending_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        BookingRequest::factory()->create(['status' => BookingRequestStatus::PENDING]);
        BookingRequest::factory()->create(['status' => BookingRequestStatus::APPROVED]);
        BookingRequest::factory()->create(['status' => BookingRequestStatus::REJECTED]);

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('pendingBookingRequests', 1)
            );
    }

    public function test_pending_booking_requests_are_empty_for_non_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        BookingRequest::factory()->create(['status' => BookingRequestStatus::PENDING]);

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('pendingBookingRequests', [])
            );
    }
}
