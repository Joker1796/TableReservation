<?php

namespace Feature\Model\NoAuthTests;

use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoAuthReservationRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_request_dont_create_without_auth(): void
    {
        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/create',
            ReservationRequest::factory()::ARGUMENTS,
        );

        $response->assertStatus(302);
    }

    public function test_reservation_request_dont_updated_without_auth(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation-request/'.$reservationRequest->id,
            ReservationRequest::factory()::UPDATED_ARGUMENTS,
        );

        $response->assertStatus(302);
    }

    public function test_reservation_request_dont_showed_without_auth(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/'.$reservationRequest->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_request_dont_delete_without_auth(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation-request/'.$reservationRequest->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_request_dont_attach_user_without_auth(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();
        $user = User::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/user/'.$user->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_request_dont_detach_user_without_auth(): void
    {
        $reservationRequest = ReservationRequest::factory()
            ->has(User::factory(), 'users')
            ->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/user/'.$reservationRequest->users()->first()->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_request_dont_associate_table_without_auth(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();
        $table = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/table/'.$table->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_request_dont_delete_table_without_auth(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation-request/'.$reservationRequest->id
        );

        $response->assertStatus(302);
    }
}
