<?php

namespace Feature\API;

use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationRequestTest extends TestCase
{
    use RefreshDatabase;

    public function acting(): User
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get('/');

        return $user;
    }

    public function test_reservation_request_created_successfully(): void
    {
        $user = $this->acting();

        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/create',
            ReservationRequest::factory()::ARGUMENTS,
        );

        $response->assertOk();

        $this->assertArrayHasKey('author_id', $response->json());
        $this->assertEquals($user->id, $response->json('author_id'));
    }

    public function test_reservation_request_created_with_table_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $arguments = ReservationRequest::factory()::ARGUMENTS;
        $arguments['table_id'] = $table->id;

        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/create',
            $arguments
        );

        $response->assertOk();

        $this->assertArrayHasKey('table_id', $response->json());
        $this->assertEquals($table->id, $response->json('table_id'));
    }

    public function test_reservation_request_created_with_author_successfully(): void
    {
        $this->acting();

        $user = User::factory()->create();

        $arguments = ReservationRequest::factory()::ARGUMENTS;
        $arguments['author'] = $user->id;

        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/create',
            $arguments
        );

        $response->assertOk();

        $this->assertArrayHasKey('author_id', $response->json());
        $this->assertEquals($user->id, $response->json('author_id'));
    }

    public function test_reservation_request_created_with_users_successfully(): void
    {
        $this->acting();

        $users = User::factory()
            ->count(2)
            ->create();

        $arguments = ReservationRequest::factory()::ARGUMENTS;
        $arguments['users'] = [$users->pluck('id')->toArray()];

        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/create',
            $arguments
        );

        $response->assertOk();
    }

    public function test_reservation_request_updated_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation-request/'.$reservationRequest->id,
            ReservationRequest::factory()::UPDATED_ARGUMENTS,
        );

        $response->assertOk();
        $response->assertJsonFragment(ReservationRequest::factory()::UPDATED_ARGUMENTS);
    }

    public function test_reservation_request_showed_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/'.$reservationRequest->id
        );

        $response->assertOk();

        $this->assertEquals($reservationRequest->id, $response->json('id'));
    }

    public function test_reservation_request_soft_delete_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation-request/'.$reservationRequest->id
        );

        $response->assertOk();

        $this->assertArrayHasKey('deleted_at', $response->json());
        $this->assertNotNull($response->json('deleted_at'));
    }

    public function test_reservation_request_attach_user_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();
        $user = User::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/user/'.$user->id
        );

        $response->assertOk();

        $this->assertDatabaseHas('reservation_request_user', [
            'user_id' => $user->id,
            'reservation_request_id' => $reservationRequest->id,
        ]);
    }

    public function test_reservation_request_detach_user_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()
            ->has(User::factory(), 'users')
            ->create();

        $userId = $reservationRequest->users()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/user/'.$userId
        );

        $response->assertOk();

        $this->assertDatabaseMissing('reservation_request_user', [
            'user_id' => $userId,
            'reservation_request_id' => $reservationRequest->id,
        ]);
    }

    public function test_reservation_request_associate_table_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();
        $table = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/table/'.$table->id
        );

        $response->assertOk();

        $this->assertArrayHasKey('table_id', $response->json());
        $this->assertEquals($response->json('table_id'), $table->id);
        $this->assertArrayHasKey('table', $response->json());
        $this->assertNotNull($response->json('table'));
        $this->assertEquals($response->json('table.id'), $table->id);
    }

    public function test_reservation_request_delete_table_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();

        $this->assertNotNull($reservationRequest->table()->first()->id);

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/table',
        );

        $response->assertOk();

        $this->assertArrayHasKey('table_id', $response->json());
        $this->assertNull($response->json('table_id'));
        $this->assertArrayHasKey('table', $response->json());
        $this->assertNull($response->json('table'));
    }
}
