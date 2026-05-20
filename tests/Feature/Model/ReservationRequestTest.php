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

    const array BASE_ATTRIBUTES = [
        'comment' => 'test comment',
        'date' => '01.01.2026 00:00:00',
        'hours' => 1,
        'status' => 0,
    ];

    const array UPDATED_BASIC_ATTRIBUTES = [
        'comment' => 'test comment updated',
        'date' => '2026-01-02T10:00:00.000000Z',
        'hours' => 4,
        'status' => 1,
    ];

    public function acting(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['banned' => false])
            ->get('/');
    }

    public function test_reservation_request_created_successfully(): void
    {
        $this->acting();

        $response = $this->call('GET', '/api-V1/reservation-request/create', self::BASE_ATTRIBUTES);

        $response->assertOk();
    }

    public function test_reservation_request_with_table_created_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $arguments = self::BASE_ATTRIBUTES;
        $arguments['table_id'] = $table->id;

        $response = $this->call('GET', '/api-V1/reservation-request/create', $arguments);

        $response->assertOk();

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('table_id', $content);
        $this->assertEquals($table->id, $content['table_id']);
    }

    public function test_reservation_request_with_one_user_created_successfully(): void
    {
        $this->acting();

        $user = User::factory()->create();

        $arguments = self::BASE_ATTRIBUTES;
        $arguments['users'] = [$user->id];

        $response = $this->call('GET', '/api-V1/reservation-request/create', $arguments);

        $response->assertOk();
    }

    public function test_reservation_request_with_two_users_created_successfully(): void
    {
        $this->acting();

        $users = User::factory()
            ->count(2)
            ->create();

        $arguments = self::BASE_ATTRIBUTES;
        $arguments['users'] = [$users->pluck('id')->toArray()];

        $response = $this->call('GET', '/api-V1/reservation-request/create', $arguments);

        $response->assertOk();
    }

    public function test_reservation_request_updated_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api-V1/reservation-request/'.$reservationRequest->id,
            self::UPDATED_BASIC_ATTRIBUTES
        );

        $response->assertOk();
        $response->assertJsonFragment(self::UPDATED_BASIC_ATTRIBUTES);
    }

    public function test_reservation_request_showed_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call('GET', '/api-V1/reservation-request/'.$reservationRequest->id);

        $response->assertOk();
    }

    public function test_reservation_request_soft_delete_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();

        $response = $this->call('DELETE', '/api-V1/reservation-request/'.$reservationRequest->id);

        $response->assertOk();

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('deleted_at', $content);
        $this->assertNotNull($content['deleted_at']);
    }

    public function test_reservation_request_attach_user_successfully(): void
    {
        $this->acting();

        $reservationRequest = ReservationRequest::factory()->create();
        $user = User::factory()->create();

        $response = $this->call(
            'PUT',
            '/api-V1/reservation-request/'.$reservationRequest->id.'/user/'.$user->id
        );

        $response->assertOk();
    }

    public function test_reservation_request_detach_user_successfully(): void
    {
        $this->acting();

        $reservation = ReservationRequest::factory()
            ->has(User::factory())
            ->create();

        $responseDetach = $this->call(
            'DELETE',
            '/api-V1/reservation-request/'.$reservation->id.'/user/'.$reservation->user()->first()->id
        );

        $responseDetach->assertOk();
    }
}
