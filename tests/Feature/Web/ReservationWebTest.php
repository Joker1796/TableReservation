<?php

namespace Tests\Feature\Web;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
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

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        return $admin;
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

    public function test_guests_are_redirected_from_attach_user(): void
    {
        $reservation = Reservation::factory()->create();
        $user = User::factory()->create();
        $this->put(route('reservations.user.attach', [$reservation->id, $user->id]))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_detach_user(): void
    {
        $reservation = Reservation::factory()->has(User::factory())->create();
        $userId = $reservation->users()->first()->id;
        $this->delete(route('reservations.user.detach', [$reservation->id, $userId]))
            ->assertRedirect(route('login'));
    }

    // --- non-participant forbidden ---

    public function test_non_participant_is_forbidden_from_attach_user(): void
    {
        $user = $this->actingAsUser();
        $reservation = Reservation::factory()->create();
        $other = User::factory()->create();

        $this->put(route('reservations.user.attach', [$reservation->id, $other->id]))
            ->assertForbidden();

        $this->assertDatabaseMissing('reservation_user', [
            'reservation_id' => $reservation->id,
            'user_id' => $other->id,
        ]);
    }

    public function test_non_participant_is_forbidden_from_detach_user(): void
    {
        $this->actingAsUser();
        $reservation = Reservation::factory()->has(User::factory())->create();
        $userId = $reservation->users()->first()->id;

        $this->delete(route('reservations.user.detach', [$reservation->id, $userId]))
            ->assertForbidden();

        $this->assertDatabaseHas('reservation_user', [
            'reservation_id' => $reservation->id,
            'user_id' => $userId,
        ]);
    }

    // --- non-admin forbidden ---

    public function test_non_admin_is_forbidden_from_create(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('reservations.create'))->assertForbidden();
    }

    public function test_non_admin_is_forbidden_from_store(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post(route('reservations.store'), Reservation::factory()::ARGUMENTS)->assertForbidden();
    }

    // --- admin happy path ---

    public function test_user_can_view_reservations_index(): void
    {
        $this->actingAsUser();
        $this->get(route('reservations.index'))
            ->assertOk();
    }

    public function test_index_returns_only_reservations_for_given_date(): void
    {
        $this->actingAsUser();

        Reservation::factory()->create(['date' => '2026-07-10']);
        Reservation::factory()->create(['date' => '2026-07-10']);
        Reservation::factory()->create(['date' => '2026-07-11']);

        $this->get(route('reservations.index', ['date' => '2026-07-10']))
            ->assertInertia(fn (Assert $page) => $page
                ->component('reservations/Index')
                ->has('reservations', 2)
            );
    }

    public function test_index_defaults_to_today_when_no_date_given(): void
    {
        $this->actingAsUser();

        Reservation::factory()->create(['date' => now()->toDateString()]);
        Reservation::factory()->create(['date' => now()->addDay()->toDateString()]);

        $this->get(route('reservations.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('reservations', 1)
            );
    }

    public function test_index_returns_empty_when_no_reservations_on_date(): void
    {
        $this->actingAsUser();

        Reservation::factory()->create(['date' => '2026-07-10']);

        $this->get(route('reservations.index', ['date' => '2026-08-01']))
            ->assertInertia(fn (Assert $page) => $page
                ->has('reservations', 0)
            );
    }

    public function test_user_can_view_reservation(): void
    {
        $this->actingAsUser();
        $reservation = Reservation::factory()->create();

        $this->get(route('reservations.show', $reservation->id))
            ->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAsAdmin();
        $this->get(route('reservations.create'))
            ->assertOk();
    }

    public function test_admin_can_create_reservation(): void
    {
        $this->actingAsAdmin();

        $this->post(route('reservations.store'), Reservation::factory()::ARGUMENTS)
            ->assertRedirect(route('reservations.index'));

        $this->assertDatabaseHas('reservations', ['comment' => 'test comment']);
    }

    public function test_reservation_not_created_without_date(): void
    {
        $this->actingAsAdmin();

        $data = Reservation::factory()::ARGUMENTS;
        unset($data['date']);

        $this->post(route('reservations.store'), $data)
            ->assertSessionHasErrors(['date']);
    }

    public function test_admin_can_view_edit_form(): void
    {
        $this->actingAsAdmin();
        $reservation = Reservation::factory()->create();

        $this->get(route('reservations.edit', $reservation->id))
            ->assertOk();
    }

    public function test_admin_can_update_reservation(): void
    {
        $this->actingAsAdmin();
        $reservation = Reservation::factory()->create();

        $this->put(route('reservations.update', $reservation->id), Reservation::factory()::UPDATED_ARGUMENTS)
            ->assertRedirect(route('reservations.show', $reservation->id));

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'comment' => 'test comment updated',
        ]);
    }

    public function test_admin_can_delete_reservation(): void
    {
        $this->actingAsAdmin();
        $reservation = Reservation::factory()->create();

        $this->delete(route('reservations.destroy', $reservation->id))
            ->assertRedirect(route('reservations.index'));

        $this->assertSoftDeleted('reservations', ['id' => $reservation->id]);
    }

    // --- participant user management ---

    public function test_participant_can_attach_user(): void
    {
        $participant = $this->actingAsUser();
        $reservation = Reservation::factory()->create();
        $reservation->users()->attach($participant->id);
        $other = User::factory()->create();

        $this->put(route('reservations.user.attach', [$reservation->id, $other->id]))
            ->assertRedirect();

        $this->assertDatabaseHas('reservation_user', [
            'reservation_id' => $reservation->id,
            'user_id' => $other->id,
        ]);
    }

    public function test_participant_can_detach_user(): void
    {
        $participant = $this->actingAsUser();
        $other = User::factory()->create();

        $reservation = Reservation::factory()->create();
        $reservation->users()->attach([$participant->id, $other->id]);

        $this->delete(route('reservations.user.detach', [$reservation->id, $other->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('reservation_user', [
            'reservation_id' => $reservation->id,
            'user_id' => $other->id,
        ]);
    }

    public function test_participant_can_detach_themselves(): void
    {
        $participant = $this->actingAsUser();
        $reservation = Reservation::factory()->create();
        $reservation->users()->attach($participant->id);

        $this->delete(route('reservations.user.detach', [$reservation->id, $participant->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('reservation_user', [
            'reservation_id' => $reservation->id,
            'user_id' => $participant->id,
        ]);
    }
}
