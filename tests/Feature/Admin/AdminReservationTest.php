<?php

namespace Tests\Feature\Admin;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminReservationTest extends TestCase
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
        $this->get(route('admin.reservations.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_destroy(): void
    {
        $reservation = Reservation::factory()->create();
        $this->delete(route('admin.reservations.destroy', $reservation->id))
            ->assertRedirect(route('login'));
    }

    // --- non-admin forbidden ---

    public function test_non_admin_is_forbidden_from_index(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.reservations.index'))
            ->assertForbidden();
    }

    // --- admin happy path ---

    public function test_admin_can_view_reservations_index(): void
    {
        $this->admin();
        $this->get(route('admin.reservations.index'))
            ->assertOk();
    }

    public function test_admin_can_send_invite(): void
    {
        $admin = $this->admin();
        $reservation = Reservation::factory()->create();
        $target = User::factory()->create();

        $this->post(route('admin.reservations.sendInvite', $reservation->id), [
            'user_id' => $target->id,
        ])
            ->assertRedirect(route('admin.reservations.index'));

        $this->assertDatabaseHas('invites', [
            'reservation_id' => $reservation->id,
            'target_id' => $target->id,
            'author_id' => $admin->id,
        ]);
    }

    public function test_admin_can_delete_reservation(): void
    {
        $this->admin();
        $reservation = Reservation::factory()->create();

        $this->delete(route('admin.reservations.destroy', $reservation->id))
            ->assertRedirect(route('admin.reservations.index'));

        $this->assertSoftDeleted('reservations', ['id' => $reservation->id]);
    }

    public function test_index_paginates_reservations(): void
    {
        $this->admin();
        Reservation::factory()->count(15)->create();

        $this->get(route('admin.reservations.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('reservations.data', 10)
                ->where('reservations.total', 15)
                ->where('reservations.current_page', 1)
            );
    }

    public function test_index_returns_second_page(): void
    {
        $this->admin();
        Reservation::factory()->count(15)->create();

        $this->get(route('admin.reservations.index', ['page' => 2]))
            ->assertInertia(fn (Assert $page) => $page
                ->has('reservations.data', 5)
                ->where('reservations.current_page', 2)
            );
    }
}
