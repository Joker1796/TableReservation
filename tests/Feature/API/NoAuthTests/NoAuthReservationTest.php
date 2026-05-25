<?php

namespace Feature\API\NoAuthTests;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoAuthReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_dont_created_without_auth(): void
    {
        $response = $this->call(
            'GET',
            '/api/V1/reservation/create',
            Reservation::factory()::ARGUMENTS,
        );

        $response->assertStatus(302);
    }

    public function test_reservation_dont_updated_without_auth(): void
    {
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation/'.$reservation->id,
            Reservation::factory()::UPDATED_ARGUMENTS,
        );

        $response->assertStatus(302);
    }

    public function test_reservation_dont_showed_without_auth(): void
    {
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/reservation/'.$reservation->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_dont_delete_without_auth(): void
    {
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation/'.$reservation->id
        );

        $response->assertStatus(302);

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
    }

    public function test_reservation_dont_attach_user_without_auth(): void
    {
        $reservation = Reservation::factory()->create();
        $user = User::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation/'.$reservation->id.'/user/'.$user->id
        );

        $response->assertStatus(302);

        $this->assertDatabaseMissing('reservation_user', [
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
        ]);
    }

    public function test_reservation_dont_detach_user_without_auth(): void
    {
        $reservation = Reservation::factory()
            ->has(User::factory())
            ->create();

        $userId = $reservation->users()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation/'.$reservation->id.'/user/'.$userId
        );

        $response->assertStatus(302);

        $this->assertDatabaseHas('reservation_user', [
            'user_id' => $userId,
            'reservation_id' => $reservation->id,
        ]);
    }
}
