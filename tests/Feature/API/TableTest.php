<?php

namespace Feature\API;

use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableTest extends TestCase
{
    use RefreshDatabase;

    public function acting(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['banned' => false])
            ->get('/');
    }

    public function test_table_created_successfully(): void
    {
        $this->acting();

        $response = $this->call(
            'GET',
            '/api/V1/table/create',
            Table::factory()::ARGUMENTS,
        );

        $response->assertOk();
    }

    public function test_table_created_with_nullable_description_successfully(): void
    {
        $this->acting();

        $arguments = Table::factory()::ARGUMENTS;
        $arguments['description'] = null;

        $response = $this->call(
            'GET',
            '/api/V1/table/create',
            $arguments,
        );

        $response->assertOk();
    }

    public function test_table_dont_created_with_incorrect_status(): void
    {
        $this->acting();

        $arguments = Table::factory()::ARGUMENTS;
        $arguments['status'] = 'incorrect';

        $response = $this->get('/api/V1/table/create', $arguments);

        $response->assertStatus(302);
    }

    public function test_table_updated_successfully(): void
    {
        $this->acting();

        $weapon = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$weapon->id,
            Table::factory()::UPDATED_ARGUMENTS,
        );

        $response->assertOk();
        $response->assertJsonFragment(Table::factory()::UPDATED_ARGUMENTS);
    }

    public function test_table_dont_updated_with_incorrect_status(): void
    {
        $this->acting();

        $weapon = Table::factory()->create();
        $arguments = Table::factory()::ARGUMENTS;
        $arguments['status'] = 'incorrect';

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$weapon->id,
            $arguments,
        );

        $response->assertStatus(302);
    }

    public function test_table_showed_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/table/'.$table->id
        );

        $response->assertOk();

        $this->assertEquals($table->id, $response->json('id'));
    }

    public function test_table_soft_delete_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/table/'.$table->id
        );

        $response->assertOk();

        $this->assertNotNull($response->json('deleted_at'));
        $this->assertDatabaseHas('tables', ['id' => $table->id]);
    }

    public function test_table_add_reservation_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$table->id.'/reservation/'.$reservation->id
        );

        $response->assertOk();

        $this->assertNotEmpty($table->fresh()->reservations->where('id', $reservation->id));
    }

    public function test_table_delete_reservation_successfully(): void
    {
        $this->acting();

        $table = Table::factory()
            ->has(Reservation::factory()->count(3))
            ->create();

        $reservationId = $table->reservations()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/table/'.$table->id.'/reservation/'.$reservationId
        );

        $response->assertOk();

        $this->assertEmpty($table->fresh()->reservations->where('id', $reservationId));
    }

    public function test_table_add_reservation_request_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$table->id.'/reservation-request/'.$reservationRequest->id
        );

        $response->assertOk();

        $this->assertNotEmpty($table->fresh()->reservationRequests->where('id', $reservationRequest->id));
    }

    public function test_table_delete_reservation_request_successfully(): void
    {
        $this->acting();

        $table = Table::factory()
            ->has(ReservationRequest::factory()->count(3))
            ->create();

        $reservationRequestId = $table->reservationRequests()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/table/'.$table->id.'/reservation-request/'.$reservationRequestId
        );

        $response->assertOk();

        $this->assertEmpty($table->fresh()->reservationRequests->where('id', $reservationRequestId));
    }

    public function test_table_dont_created_without_required_name(): void
    {
        $this->acting();

        $arguments = Table::factory()::ARGUMENTS;
        unset($arguments['name']);

        $response = $this->call('GET', '/api/V1/table/create', $arguments);

        $response->assertStatus(302);
    }

    public function test_table_show_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('GET', '/api/V1/table/99999');

        $response->assertNotFound();
    }

    public function test_table_update_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('PUT', '/api/V1/table/99999', Table::factory()::UPDATED_ARGUMENTS);

        $response->assertNotFound();
    }

    public function test_table_soft_delete_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('DELETE', '/api/V1/table/99999');

        $response->assertNotFound();
    }
}
