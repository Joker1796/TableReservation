<?php

namespace Feature\Model;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    const array BASE_ARGUMENTS = [
        'comment' => 'test comment',
        'date' => '01.01.2026 00:00:00',
        'hours' => '1',
    ];
    const array UPDATED_BASIC_ARGUMENTS = [
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

        $response = $this->call(
            'GET',
            '/api/V1/reservation/create',
            self::BASE_ARGUMENTS
        );

        $response->assertOk();
    }

    public function test_reservation_created_not_successfully_code_302(): void
    {
        $response = $this->call(
            'GET',
            '/api/V1/reservation/create',
            self::BASE_ARGUMENTS
        );

        $response->assertStatus(302);
    }

    public function test_reservation_with_users_created_successfully(): void
    {
        $this->acting();

        $users = User::factory()
            ->count(2)
            ->create();

        $arguments = self::BASE_ARGUMENTS;
        $arguments['users'] = [$users->pluck('id')->toArray()];

        $response = $this->call(
            'GET',
            '/api/V1/reservation/create',
            $arguments
        );

        $response->assertOk();
    }

    public function test_reservation_with_table_created_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $arguments = self::BASE_ARGUMENTS;
        $arguments['table'] = $table->id;

        $response = $this->call(
            'GET',
            '/api/V1/reservation/create',
            $arguments
        );

        $response->assertOk();

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('table_id', $content);
        $this->assertEquals($table->id, $content['table_id']);
        $this->assertArrayHasKey('table', $content);
        $this->assertEquals($table->id, $content['table']['id']);
    }

    public function test_reservation_updated_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation/'.$reservation->id,
            self::UPDATED_BASIC_ARGUMENTS
        );

        $response->assertOk();
        $response->assertJsonFragment(self::UPDATED_BASIC_ARGUMENTS);
    }

    public function test_reservation_updated_not_successfully_code_302(): void
    {
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation/'.$reservation->id,
            self::UPDATED_BASIC_ARGUMENTS
        );

        $response->assertStatus(302);
    }

    public function test_reservation_showed_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/reservation/'.$reservation->id
        );

        $response->assertOk();
    }

    public function test_reservation_showed_not_successfully_code_302(): void
    {
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/reservation/'.$reservation->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_soft_delete_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation/'.$reservation->id
        );

        $response->assertOk();

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('deleted_at', $content);
        $this->assertNotNull($content['deleted_at']);
    }

    public function test_reservation_soft_delete_not_successfully_code_302(): void
    {
        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation/'.$reservation->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_attach_user_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()->create();
        $user = User::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation/'.$reservation->id.'/user/'.$user->id
        );

        $response->assertOk();

        $this->assertDatabaseHas('reservation_user', [
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
        ]);
    }

    public function test_reservation_attach_user_not_successfully_code_302(): void
    {
        $reservation = Reservation::factory()->create();
        $user = User::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation/'.$reservation->id.'/user/'.$user->id
        );

        $response->assertStatus(302);
    }

    public function test_reservation_detach_user_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()
            ->has(User::factory())
            ->create();

        $userId = $reservation->users()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation/'.$reservation->id.'/user/'.$userId
        );

        $response->assertOk();

        $this->assertDatabaseMissing('reservation_user', [
            'user_id' => $userId,
            'reservation_id' => $reservation->id,
        ]);
    }

    public function test_reservation_detach_user_not_successfully_code_302(): void
    {
        $reservation = Reservation::factory()
            ->has(User::factory())
            ->create();

        $userId = $reservation->users()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation/'.$reservation->id.'/user/'.$userId
        );

        $response->assertStatus(302);
    }
}
