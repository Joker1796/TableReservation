<?php

namespace Feature\API\NoAuthTests;

use App\Models\BookingRequest;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoAuthTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_dont_created_without_auth(): void
    {
        $response = $this->call(
            'GET',
            '/api/V1/table/create',
            Table::factory()::ARGUMENTS,
        );

        $response->assertStatus(302);
    }

    public function test_table_dont_updated_without_auth(): void
    {
        $weapon = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$weapon->id,
            Table::factory()::UPDATED_ARGUMENTS,
        );

        $response->assertStatus(302);
    }

    public function test_table_dont_showed_without_auth(): void
    {
        $table = Table::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/table/'.$table->id
        );

        $response->assertStatus(302);
    }

    public function test_table_dont_delete_without_auth(): void
    {
        $table = Table::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/table/'.$table->id
        );

        $response->assertStatus(302);
    }

    public function test_table_dont_add_reservation_without_auth(): void
    {
        $table = Table::factory()->create();
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$table->id.'/reservation/'.$reservation->id
        );

        $response->assertStatus(302);

        $this->assertEmpty($table->fresh()->reservations->where('id', $reservation->id));
    }

    public function test_table_dont_delete_reservation_without_auth(): void
    {
        $table = Table::factory()
            ->has(Reservation::factory()->count(3))
            ->create();

        $reservationId = $table->reservations()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/table/'.$table->id.'/reservation/'.$reservationId
        );

        $response->assertStatus(302);

        $this->assertNotEmpty($table->fresh()->reservations->where('id', $reservationId));
    }

    public function test_table_dont_add_booking_request_without_auth(): void
    {
        $table = Table::factory()->create();
        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$table->id.'/booking-request/'.$bookingRequest->id
        );

        $response->assertStatus(302);

        $this->assertEmpty($table->fresh()->bookingRequests->where('id', $bookingRequest->id));
    }

    public function test_table_dont_delete_booking_request_without_auth(): void
    {
        $table = Table::factory()
            ->has(BookingRequest::factory()->count(3))
            ->create();

        $bookingRequestId = $table->bookingRequests()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/table/'.$table->id.'/booking-request/'.$bookingRequestId
        );

        $response->assertStatus(302);

        $this->assertNotEmpty($table->fresh()->bookingRequests->where('id', $bookingRequestId));
    }
}
