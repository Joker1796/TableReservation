<?php

namespace Tests\Feature\Web;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationWebTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    private function ownReservation(User $user): Reservation
    {
        $reservation = Reservation::factory()->create();
        $reservation->users()->attach($user->id);

        return $reservation;
    }

    // --- guest redirects ---

    public function test_guests_are_redirected_from_index(): void
    {
        $this->get(route('reservations.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_create(): void
    {
        $this->get(route('reservations.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_store(): void
    {
        $this->post(route('reservations.store'), Reservation::factory()::ARGUMENTS)
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_show(): void
    {
        $reservation = Reservation::factory()->create();
        $this->get(route('reservations.show', $reservation->id))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_edit(): void
    {
        $reservation = Reservation::factory()->create();
        $this->get(route('reservations.edit', $reservation->id))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_update(): void
    {
        $reservation = Reservation::factory()->create();
        $this->put(route('reservations.update', $reservation->id), Reservation::factory()::UPDATED_ARGUMENTS)
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_destroy(): void
    {
        $reservation = Reservation::factory()->create();
        $this->delete(route('reservations.destroy', $reservation->id))
            ->assertRedirect(route('login'));
    }

    // --- index / create / show ---

    public function test_user_can_view_reservations_index(): void
    {
        $this->actingAsUser();
        $this->get(route('reservations.index'))
            ->assertOk();
    }

    public function test_user_can_view_create_form(): void
    {
        $this->actingAsUser();
        $this->get(route('reservations.create'))
            ->assertOk();
    }

    public function test_user_can_view_reservation(): void
    {
        $this->actingAsUser();
        $reservation = Reservation::factory()->create();

        $this->get(route('reservations.show', $reservation->id))
            ->assertOk();
    }

    // --- store ---

    public function test_user_can_create_reservation(): void
    {
        $this->actingAsUser();

        $this->post(route('reservations.store'), Reservation::factory()::ARGUMENTS)
            ->assertRedirect(route('reservations.index'));

        $this->assertDatabaseHas('reservations', ['comment' => 'test comment']);
    }

    public function test_reservation_not_created_without_date(): void
    {
        $this->actingAsUser();

        $data = Reservation::factory()::ARGUMENTS;
        unset($data['date']);

        $this->post(route('reservations.store'), $data)
            ->assertSessionHasErrors(['date']);
    }

    // --- edit / update ---

    public function test_user_can_view_edit_form_for_own_reservation(): void
    {
        $user = $this->actingAsUser();
        $reservation = $this->ownReservation($user);

        $this->get(route('reservations.edit', $reservation->id))
            ->assertOk();
    }

    public function test_user_cannot_view_edit_form_for_others_reservation(): void
    {
        $this->actingAsUser();
        $reservation = Reservation::factory()->create();

        $this->get(route('reservations.edit', $reservation->id))
            ->assertNotFound();
    }

    public function test_user_can_update_own_reservation(): void
    {
        $user = $this->actingAsUser();
        $reservation = $this->ownReservation($user);

        $this->put(route('reservations.update', $reservation->id), Reservation::factory()::UPDATED_ARGUMENTS)
            ->assertRedirect(route('reservations.show', $reservation->id));

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'comment' => 'test comment updated',
        ]);
    }

    public function test_user_cannot_update_others_reservation(): void
    {
        $this->actingAsUser();
        $reservation = Reservation::factory()->create();

        $this->put(route('reservations.update', $reservation->id), Reservation::factory()::UPDATED_ARGUMENTS)
            ->assertNotFound();
    }

    // --- destroy ---

    public function test_user_can_delete_own_reservation(): void
    {
        $user = $this->actingAsUser();
        $reservation = $this->ownReservation($user);

        $this->delete(route('reservations.destroy', $reservation->id))
            ->assertRedirect(route('reservations.index'));

        $this->assertSoftDeleted('reservations', ['id' => $reservation->id]);
    }

    public function test_user_cannot_delete_others_reservation(): void
    {
        $this->actingAsUser();
        $reservation = Reservation::factory()->create();

        $this->delete(route('reservations.destroy', $reservation->id))
            ->assertNotFound();
    }
}
