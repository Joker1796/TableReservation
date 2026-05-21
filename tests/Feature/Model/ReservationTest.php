<?php

namespace Feature\Model;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    const array BASE_ATTRIBUTES = [
        'comment' => 'test comment',
        'date' => '01.01.2026 00:00:00',
        'hours' => '1',
    ];
    const array UPDATED_BASIC_ATTRIBUTES = [
        'comment' => 'test comment updated',
        'date' => '02.01.2026 10:00:00',
        'hours' => '4',
    ];

    public function acting(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['banned' => false])
            ->get('/');
    }

    public function test_reservation_created_successfully(): void
    {
        $this->acting();

        $response = $this->call('GET', '/api/V1/reservation/create', self::BASE_ATTRIBUTES);

        $response->assertOk();
    }

    public function test_reservation_with_one_user_created_successfully(): void
    {
        $this->acting();

        $user = User::factory()->create();

        $arguments = self::BASE_ATTRIBUTES;
        $arguments['users'] = [$user->id];

        $response = $this->call('GET', '/api/V1/reservation/create', $arguments);

        $response->assertOk();
    }

    public function test_reservation_with_two_users_created_successfully(): void
    {
        $this->acting();

        $users = User::factory()
            ->count(2)
            ->create();

        $arguments = self::BASE_ATTRIBUTES;
        $arguments['users'] = [$users->pluck('id')->toArray()];

        $response = $this->call('GET', '/api/V1/reservation/create', $arguments);

        $response->assertOk();
    }

    public function test_reservation_updated_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()->create();

        $response = $this->call('PUT', '/api/V1/reservation/'.$reservation->id, self::UPDATED_BASIC_ATTRIBUTES);

        $response->assertOk();
        $response->assertJsonFragment(self::UPDATED_BASIC_ATTRIBUTES);
    }

    public function test_reservation_showed_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()->create();

        $response = $this->call('GET', '/api/V1/reservation/'.$reservation->id);

        $response->assertOk();
    }

    public function test_reservation_soft_delete_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()->create();

        $response = $this->call('DELETE', '/api/V1/reservation/'.$reservation->id);

        $response->assertOk();

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('deleted_at', $content);
        $this->assertNotNull($content['deleted_at']);
    }

    public function test_reservation_attach_user_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()->create();
        $user = User::factory()->create();

        $response = $this->call('PUT', '/api/V1/reservation/'.$reservation->id.'/user/'.$user->id);

        $response->assertOk();
    }

    public function test_reservation_detach_user_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()
            ->has(User::factory())
            ->create();

        $responseDetach = $this->call(
            'DELETE',
            '/api/V1/reservation/'.$reservation->id.'/user/'.$reservation->users()->first()->id
        );

        $responseDetach->assertOk();
    }
}
