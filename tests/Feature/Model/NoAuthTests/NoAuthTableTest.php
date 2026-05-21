<?php

namespace Feature\Model\NoAuthTests;

use App\Models\Reservation;
use App\Models\ReservationRequest;
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

    public function test_table_dont_add_reservation_request_without_auth(): void
    {
        $table = Table::factory()->create();
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$table->id.'/reservation-request/'.$reservationRequest->id
        );

        $response->assertStatus(302);

        $this->assertEmpty($table->fresh()->reservationRequests->where('id', $reservationRequest->id));
    }

    public function test_table_dont_delete_reservation_request_without_auth(): void
    {
        $table = Table::factory()
            ->has(ReservationRequest::factory()->count(3))
            ->create();

        $reservationRequestId = $table->reservationRequests()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/table/'.$table->id.'/reservation-request/'.$reservationRequestId
        );

        $response->assertStatus(302);

        $this->assertNotEmpty($table->fresh()->reservationRequests->where('id', $reservationRequestId));
    }
}
