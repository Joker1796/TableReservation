<?php

namespace Feature\Model;

use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationRequestTest extends TestCase
{
    use RefreshDatabase;

    const array BASE_ARGUMENTS = [
        'comment' => 'test comment',
        'date' => '01.01.2026 00:00:00',
        'hours' => 1,
        'status' => 0,
    ];

    const array UPDATED_BASIC_ARGUMENTS = [
        'comment' => 'test comment updated',
        'date' => '2026-01-02T10:00:00.000000Z',
        'hours' => 4,
        'status' => 1,
    ];

    public function acting()
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
            self::BASE_ARGUMENTS
        );

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('author_id', $content);
        $this->assertEquals($user->id, $content['author_id']);

        $response->assertOk();
    }

    public function test_reservation_request_created_not_successfully_code_302(): void
    {
        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/create',
            self::BASE_ARGUMENTS
        );

        $response->assertStatus(302);
    }

    public function test_reservation_request_with_table_created_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $arguments = self::BASE_ARGUMENTS;
        $arguments['table_id'] = $table->id;

        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/create',
            $arguments
        );

        $response->assertOk();

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('table_id', $content);
        $this->assertEquals($table->id, $content['table_id']);
    }

    public function test_reservation_request_with_author_created_successfully(): void
    {
        $this->acting();

        $user = User::factory()->create();

        $arguments = self::BASE_ARGUMENTS;
        $arguments['author'] = $user->id;

        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/create',
            $arguments
        );

        $response->assertOk();

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('author_id', $content);
        $this->assertEquals($user->id, $content['author_id']);
    }

    public function test_reservation_request_with_users_created_successfully(): void
    {
        $this->acting();

        $users = User::factory()
            ->count(2)
            ->create();

        $arguments = self::BASE_ARGUMENTS;
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
            self::UPDATED_BASIC_ARGUMENTS
        );

        $response->assertOk();
        $response->assertJsonFragment(self::UPDATED_BASIC_ARGUMENTS);
    }

    public function test_reservation_request_updated_not_successfully_code_302(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation-request/'.$reservationRequest->id,
            self::UPDATED_BASIC_ARGUMENTS
        );

        $response->assertStatus(302);
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
    }

    public function test_reservation_request_showed_not_successfully_code_302(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/reservation-request/'.$reservationRequest->id
        );

        $response->assertStatus(302);
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

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('deleted_at', $content);
        $this->assertNotNull($content['deleted_at']);
    }

    public function test_reservation_request_soft_delete_not_successfully_code_302(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation-request/'.$reservationRequest->id
        );

        $response->assertStatus(302);
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
    }

    public function test_reservation_request_attach_user_not_successfully_code_302(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();
        $user = User::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/user/'.$user->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_request_detach_user_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()
            ->has(User::factory(), 'users')
            ->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/user/'.$reservationRequest->users()->first()->id
        );

        $response->assertOk();
    }

    public function test_reservation_request_detach_user_not_successfully_code_302(): void
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

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('table_id', $content);
        $this->assertEquals($content['table_id'], $table->id);
        $this->assertArrayHasKey('table', $content);
        $this->assertNotNull($content['table']);
        $this->assertEquals($content['table']['id'], $table->id);
    }

    public function test_reservation_request_associate_table_not_successfully_code_302(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();
        $table = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation-request/'.$reservationRequest->id.'/table/'.$table->id
        );

        $response->assertStatus(302);
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

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('table_id', $content);
        $this->assertNull($content['table_id']);
        $this->assertArrayHasKey('table', $content);
        $this->assertNull($content['table']);
    }

    public function test_reservation_request_delete_table_not_successfully_code_302(): void
    {
        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation-request/'.$reservationRequest->id
        );

        $response->assertStatus(302);
    }
}
