<?php

namespace Tests\Feature\Admin;

use App\Enums\BookingRequestStatus;
use App\Models\BookingRequest;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingRequestTest extends TestCase
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
        $this->get(route('admin.requests.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_update_status(): void
    {
        $br = BookingRequest::factory()->create();
        $this->put(route('admin.requests.updateStatus', $br->id), [
            'status' => BookingRequestStatus::REJECTED->value,
        ])
            ->assertRedirect(route('login'));
    }

    // --- non-admin forbidden ---

    public function test_non_admin_is_forbidden_from_index(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.requests.index'))
            ->assertForbidden();
    }

    public function test_non_admin_is_forbidden_from_update_status(): void
    {
        $br = BookingRequest::factory()->create();
        $this->actingAs(User::factory()->create());
        $this->put(route('admin.requests.updateStatus', $br->id), [
            'status' => BookingRequestStatus::REJECTED->value,
        ])
            ->assertForbidden();
    }

    // --- admin happy path ---

    public function test_admin_can_view_requests_index(): void
    {
        $this->admin();
        $this->get(route('admin.requests.index'))
            ->assertOk();
    }

    public function test_admin_can_reject_request(): void
    {
        $this->admin();
        $br = BookingRequest::factory()->create();

        $this->put(route('admin.requests.updateStatus', $br->id), [
            'status' => BookingRequestStatus::REJECTED->value,
        ])
            ->assertRedirect(route('admin.requests.index'));

        $this->assertDatabaseHas('booking_requests', [
            'id' => $br->id,
            'status' => BookingRequestStatus::REJECTED->value,
        ]);
    }

    public function test_approving_request_creates_reservation(): void
    {
        $admin = $this->admin();
        $author = User::factory()->create();
        $br = BookingRequest::factory()->create(['author_id' => $author->id]);

        $this->put(route('admin.requests.updateStatus', $br->id), [
            'status' => BookingRequestStatus::APPROVED->value,
        ])
            ->assertRedirect(route('admin.requests.index'));

        $this->assertDatabaseHas('booking_requests', [
            'id' => $br->id,
            'status' => BookingRequestStatus::APPROVED->value,
        ]);

        $this->assertDatabaseHas('reservations', [
            'date' => $br->date,
        ]);
    }

    public function test_approving_request_attaches_author_to_reservation(): void
    {
        $admin = $this->admin();
        $author = User::factory()->create();
        $br = BookingRequest::factory()->create(['author_id' => $author->id]);

        $this->put(route('admin.requests.updateStatus', $br->id), [
            'status' => BookingRequestStatus::APPROVED->value,
        ]);

        $reservation = Reservation::latest()->first();
        $this->assertDatabaseHas('reservation_user', [
            'reservation_id' => $reservation->id,
            'user_id' => $author->id,
        ]);
    }

    public function test_admin_can_assign_table_to_request(): void
    {
        $this->admin();
        $br = BookingRequest::factory()->create(['table_id' => null]);
        $table = Table::factory()->create();

        $this->put(route('admin.requests.assignTable', $br->id), [
            'table_id' => $table->id,
        ])
            ->assertRedirect(route('admin.requests.index'));

        $this->assertDatabaseHas('booking_requests', [
            'id' => $br->id,
            'table_id' => $table->id,
        ]);
    }

    public function test_admin_can_delete_request(): void
    {
        $this->admin();
        $br = BookingRequest::factory()->create();

        $this->delete(route('admin.requests.destroy', $br->id))
            ->assertRedirect(route('admin.requests.index'));

        $this->assertSoftDeleted('booking_requests', ['id' => $br->id]);
    }
}
