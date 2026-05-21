<?php

namespace Feature\Model;

use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableTest extends TestCase
{
    use RefreshDatabase;

    const array BASE_ATTRIBUTES = [
        'name' => 'test table name',
        'description' => 'test description',
        'status' => '1',
    ];

    const array UPDATED_BASIC_ATTRIBUTES = [
        'name' => 'test table name updated',
        'description' => 'test description updated',
        'status' => '0',
    ];

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
            self::BASE_ATTRIBUTES
        );

        $response->assertOk();
    }

    public function test_table_created_not_successfully_code_302(): void
    {
        $response = $this->call(
            'GET',
            '/api/V1/table/create',
            self::BASE_ATTRIBUTES
        );

        $response->assertStatus(302);
    }

    public function test_table_updated_successfully(): void
    {
        $this->acting();

        $weapon = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$weapon->id, self::UPDATED_BASIC_ATTRIBUTES
        );

        $response->assertOk();
        $response->assertJsonFragment(self::UPDATED_BASIC_ATTRIBUTES);
    }

    public function test_table_updated_not_successfully_code_302(): void
    {
        $weapon = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$weapon->id, self::UPDATED_BASIC_ATTRIBUTES
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

    public function test_table_showed_not_successfully_code_302(): void
    {
        $table = Table::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/table/'.$table->id
        );

        $response->assertStatus(302);
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

    public function test_table_soft_delete_not_successfully_code_302(): void
    {
        $table = Table::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/table/'.$table->id
        );

        $response->assertStatus(302);
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

    public function test_table_add_reservation_not_successfully_code_302(): void
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

    public function test_table_delete_reservation_not_successfully_code_302(): void
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

    public function test_table_add_reservation_request_not_successfully_code_302(): void
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

    public function test_table_delete_reservation_request_not_successfully_code_302(): void
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
