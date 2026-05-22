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
            Reservation::factory()::ARGUMENTS,
        );

        $response->assertOk();
    }

    public function test_reservation_created_with_users_successfully(): void
    {
        $this->acting();

        $users = User::factory()
            ->count(2)
            ->create();

        $arguments = Reservation::factory()::ARGUMENTS;
        $arguments['users'] = $users->pluck('id')->toArray();

        $response = $this->call(
            'GET',
            '/api/V1/reservation/create',
            $arguments
        );

        $response->assertOk();

        $users->each(function (User $user) use ($response) {
            $this->assertDatabaseHas('reservation_user', [
                'reservation_id' => $response->json('id'),
                'user_id' => $user->id,
            ]);
        });
    }

    public function test_reservation_created_with_table_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $arguments = Reservation::factory()::ARGUMENTS;
        $arguments['table_id'] = $table->id;

        $response = $this->call(
            'GET',
            '/api/V1/reservation/create',
            $arguments
        );

        $response->assertOk();

        $this->assertArrayHasKey('table_id', $response->json());
        $this->assertEquals($table->id, $response->json('table_id'));
        $this->assertDatabaseHas('reservations', [
            'id' => $response->json('id'),
            'table_id' => $response->json('table_id'),
        ]);
    }

    public function test_reservation_updated_successfully(): void
    {
        $this->acting();

        $reservation = Reservation::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/reservation/'.$reservation->id,
            Reservation::factory()::UPDATED_ARGUMENTS,
        );

        $response->assertOk();
        $response->assertJsonFragment(Reservation::factory()::UPDATED_ARGUMENTS);
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

        $this->assertEquals($reservation->id, $response->json('id'));
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

        $this->assertNotNull($response->json('deleted_at'));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
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
}
