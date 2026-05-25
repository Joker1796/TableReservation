<?php

namespace Feature\API\NoAuthTests;

use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoAuthUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_dont_attach_reservation_without_auth(): void
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/user/'.$user->id.'/reservation/'.$reservation->id
        );

        $response->assertStatus(302);

        $this->assertDatabaseMissing('reservation_user', [
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
        ]);
    }

    public function test_user_dont_detach_reservation_without_auth(): void
    {
        $user = User::factory()
            ->has(Reservation::factory())
            ->create();

        $reservationId = $user->reservations()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/user/'.$user->id.'/reservation/'.$reservationId
        );

        $response->assertStatus(302);

        $this->assertDatabaseHas('reservation_user', [
            'user_id' => $user->id,
            'reservation_id' => $reservationId,
        ]);
    }

    public function test_user_dont_attach_reservation_request_without_auth(): void
    {
        $user = User::factory()->create();
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/user/'.$user->id.'/reservation-request/'.$reservationRequest->id
        );

        $response->assertStatus(302);

        $this->assertDatabaseMissing('reservation_request_user', [
            'user_id' => $user->id,
            'reservation_request_id' => $reservationRequest->id,
        ]);
    }

    public function test_user_dont_detach_reservation_request_without_auth(): void
    {
        $user = User::factory()
            ->has(ReservationRequest::factory())
            ->create();

        $reservationRequestId = $user->reservationRequests()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/user/'.$user->id.'/reservation-request/'.$reservationRequestId
        );

        $response->assertStatus(302);

        $this->assertDatabaseHas('reservation_request_user', [
            'user_id' => $user->id,
            'reservation_request_id' => $reservationRequestId,
        ]);
    }
}
