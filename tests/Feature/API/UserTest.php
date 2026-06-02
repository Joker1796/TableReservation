<?php

namespace Feature\API;

use App\Models\BookingRequest;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function acting(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['banned' => false])
            ->get('/');
    }

    public function test_user_attach_reservation_successfully(): void
    {
        $this->acting();

        $user = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/user/'.$user->id.'/reservation/'.$reservation->id
        );

        $response->assertOk();

        $this->assertDatabaseHas('reservation_user', [
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
        ]);
    }

    public function test_reservation_detach_user_successfully(): void
    {
        $this->acting();

        $user = User::factory()
            ->has(Reservation::factory())
            ->create();

        $reservationId = $user->reservations()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/user/'.$user->id.'/reservation/'.$reservationId
        );

        $response->assertOk();

        $this->assertDatabaseMissing('reservation_user', [
            'user_id' => $user->id,
            'reservation_id' => $reservationId,
        ]);
    }

    public function test_user_attach_booking_request_successfully(): void
    {
        $this->acting();

        $user = User::factory()->create();
        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/user/'.$user->id.'/booking-request/'.$bookingRequest->id
        );

        $response->assertOk();

        $this->assertDatabaseHas('booking_request_user', [
            'user_id' => $user->id,
            'booking_request_id' => $bookingRequest->id,
        ]);
    }

    public function test_booking_request_detach_user_successfully(): void
    {
        $this->acting();

        $user = User::factory()
            ->has(BookingRequest::factory())
            ->create();

        $bookingRequestId = $user->bookingRequests()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/user/'.$user->id.'/booking-request/'.$bookingRequestId
        );

        $response->assertOk();

        $this->assertDatabaseMissing('booking_request_user', [
            'user_id' => $user->id,
            'booking_request_id' => $bookingRequestId,
        ]);
    }

    public function test_current_user_returned_successfully(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get('/');

        $response = $this->call('GET', '/api/V1/user');

        $response->assertOk();
        $this->assertEquals($user->id, $response->json('id'));
    }
}
