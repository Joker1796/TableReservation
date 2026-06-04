<?php

namespace Feature\API;

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
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->withSession(['banned' => false])
            ->get('/');
    }

    public function test_reservation_non_admin_is_forbidden_from_create(): void
    {
        $this->actingAs(User::factory()->create())->withSession(['banned' => false])->get('/');

        $response = $this->call('GET', '/api/V1/reservation/create', Reservation::factory()::ARGUMENTS);

        $response->assertForbidden();
    }

    public function test_reservation_non_admin_is_forbidden_from_update(): void
    {
        $reservation = Reservation::factory()->create();
        $this->actingAs(User::factory()->create())->withSession(['banned' => false])->get('/');

        $response = $this->call('PUT', '/api/V1/reservation/'.$reservation->id, Reservation::factory()::UPDATED_ARGUMENTS);

        $response->assertForbidden();
    }

    public function test_reservation_non_admin_is_forbidden_from_delete(): void
    {
        $reservation = Reservation::factory()->create();
        $this->actingAs(User::factory()->create())->withSession(['banned' => false])->get('/');

        $response = $this->call('DELETE', '/api/V1/reservation/'.$reservation->id);

        $response->assertForbidden();
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
        $arguments['user_ids'] = $users->pluck('id')->toArray();

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
        $this->actingAs(User::factory()->create())->withSession(['banned' => false])->get('/');

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
        $participant = User::factory()->create();
        $this->actingAs($participant)->withSession(['banned' => false])->get('/');

        $reservation = Reservation::factory()->create();
        $reservation->users()->attach($participant->id);

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
        $participant = User::factory()->create();
        $this->actingAs($participant)->withSession(['banned' => false])->get('/');

        $other = User::factory()->create();
        $reservation = Reservation::factory()->create();
        $reservation->users()->attach([$participant->id, $other->id]);

        $response = $this->call(
            'DELETE',
            '/api/V1/reservation/'.$reservation->id.'/user/'.$other->id
        );

        $response->assertOk();

        $this->assertDatabaseMissing('reservation_user', [
            'user_id' => $other->id,
            'reservation_id' => $reservation->id,
        ]);
    }

    public function test_reservation_non_participant_is_forbidden_from_attach_user(): void
    {
        $this->actingAs(User::factory()->create())->withSession(['banned' => false])->get('/');

        $reservation = Reservation::factory()->create();
        $user = User::factory()->create();

        $response = $this->call('PUT', '/api/V1/reservation/'.$reservation->id.'/user/'.$user->id);

        $response->assertForbidden();
    }

    public function test_reservation_non_participant_is_forbidden_from_detach_user(): void
    {
        $this->actingAs(User::factory()->create())->withSession(['banned' => false])->get('/');

        $reservation = Reservation::factory()->has(User::factory())->create();
        $userId = $reservation->users()->first()->id;

        $response = $this->call('DELETE', '/api/V1/reservation/'.$reservation->id.'/user/'.$userId);

        $response->assertForbidden();
    }

    public function test_reservation_dont_created_without_required_date(): void
    {
        $this->acting();

        $arguments = Reservation::factory()::ARGUMENTS;
        unset($arguments['date']);

        $response = $this->call('GET', '/api/V1/reservation/create', $arguments);

        $response->assertStatus(302);
    }

    public function test_reservation_created_with_multiple_users_successfully(): void
    {
        $this->acting();

        $users = User::factory()->count(3)->create();

        $arguments = Reservation::factory()::ARGUMENTS;
        $arguments['user_ids'] = $users->pluck('id')->toArray();

        $response = $this->call('GET', '/api/V1/reservation/create', $arguments);

        $response->assertOk();

        $users->each(function (User $user) use ($response) {
            $this->assertDatabaseHas('reservation_user', [
                'reservation_id' => $response->json('id'),
                'user_id' => $user->id,
            ]);
        });
    }

    public function test_reservation_show_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('GET', '/api/V1/reservation/99999');

        $response->assertNotFound();
    }

    public function test_reservation_update_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('PUT', '/api/V1/reservation/99999', Reservation::factory()::UPDATED_ARGUMENTS);

        $response->assertNotFound();
    }

    public function test_reservation_soft_delete_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('DELETE', '/api/V1/reservation/99999');

        $response->assertNotFound();
    }
}
