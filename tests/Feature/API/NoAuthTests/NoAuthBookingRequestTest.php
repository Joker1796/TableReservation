<?php

namespace Feature\API\NoAuthTests;

use App\Models\BookingRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoAuthBookingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_request_dont_create_without_auth(): void
    {
        $response = $this->call(
            'GET',
            '/api/V1/booking-request/create',
            BookingRequest::factory()::ARGUMENTS,
        );

        $response->assertStatus(302);
    }

    public function test_booking_request_dont_updated_without_auth(): void
    {
        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/booking-request/'.$bookingRequest->id,
            BookingRequest::factory()::UPDATED_ARGUMENTS,
        );

        $response->assertStatus(302);
    }

    public function test_booking_request_dont_showed_without_auth(): void
    {
        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/booking-request/'.$bookingRequest->id
        );

        $response->assertStatus(302);
    }

    public function test_booking_request_dont_delete_without_auth(): void
    {
        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/booking-request/'.$bookingRequest->id
        );

        $response->assertStatus(302);
    }

    public function test_booking_request_dont_attach_user_without_auth(): void
    {
        $bookingRequest = BookingRequest::factory()->create();
        $user = User::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/booking-request/'.$bookingRequest->id.'/user/'.$user->id
        );

        $response->assertStatus(302);
    }

    public function test_booking_request_dont_detach_user_without_auth(): void
    {
        $bookingRequest = BookingRequest::factory()
            ->has(User::factory(), 'users')
            ->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/booking-request/'.$bookingRequest->id.'/user/'.$bookingRequest->users()->first()->id
        );

        $response->assertStatus(302);
    }

    public function test_booking_request_dont_associate_table_without_auth(): void
    {
        $bookingRequest = BookingRequest::factory()->create();
        $table = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/booking-request/'.$bookingRequest->id.'/table/'.$table->id
        );

        $response->assertStatus(302);
    }

    public function test_booking_request_dont_delete_table_without_auth(): void
    {
        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/booking-request/'.$bookingRequest->id.'/table'
        );

        $response->assertStatus(302);
    }
}
