<?php

namespace Feature\Model;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_attach_reservation_successfully(): void
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $response = $this->attachReservation($user->id, $reservation->id);

        $response->assertOk();
    }

    public function test_reservation_detach_user_successfully(): void
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $responseAttach = $this->attachReservation($user->id, $reservation->id);

        $responseAttach->assertOk();

        $responseDetach = $this->call('DELETE', '/api-V1/user/'.$user->id.'/reservation/'.$reservation->id);

        $responseDetach->assertOk();
    }

    private function attachReservation($userId, $reservationId): TestResponse
    {
        return $this->call('PUT', '/api-V1/user/'.$userId.'/reservation/'.$reservationId);
    }
}
