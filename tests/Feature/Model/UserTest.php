<?php

namespace Feature\Model;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function acting(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['banned' => false])
            ->get('/');
    }

    public function test_user_attach_reservation_successfully(): void
    {
        $this->acting();

        $user = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $response = $this->call('PUT', '/api/V1/user/'.$user->id.'/reservation/'.$reservation->id);

        $response->assertOk();
    }

    public function test_reservation_detach_user_successfully(): void
    {
        $this->acting();

        $user = User::factory()
            ->has(Reservation::factory())
            ->create();

        $responseDetach = $this->call(
            'DELETE',
            '/api/V1/user/'.$user->id.'/reservation/'.$user->reservations()->first()->id
        );

        $responseDetach->assertOk();
    }
}
