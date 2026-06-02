<?php

namespace Feature\API\NoAuthTests;

use App\Models\BookingRequest;
use App\Models\Reservation;
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

    public function test_user_dont_attach_booking_request_without_auth(): void
    {
        $user = User::factory()->create();
        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/user/'.$user->id.'/booking-request/'.$bookingRequest->id
        );

        $response->assertStatus(302);

        $this->assertDatabaseMissing('booking_request_user', [
            'user_id' => $user->id,
            'booking_request_id' => $bookingRequest->id,
        ]);
    }

    public function test_user_dont_detach_booking_request_without_auth(): void
    {
        $user = User::factory()
            ->has(BookingRequest::factory())
            ->create();

        $bookingRequestId = $user->bookingRequests()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/user/'.$user->id.'/booking-request/'.$bookingRequestId
        );

        $response->assertStatus(302);

        $this->assertDatabaseHas('booking_request_user', [
            'user_id' => $user->id,
            'booking_request_id' => $bookingRequestId,
        ]);
    }

    public function test_current_user_dont_returned_without_auth(): void
    {
        $response = $this->call('GET', '/api/V1/user');

        $response->assertStatus(302);
    }
}
