<?php

namespace Feature\API\NoAuthTests;

use App\Enums\InviteStatus;
use App\Models\Invite;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoAuthInviteTest extends TestCase
{
    use RefreshDatabase;

    public function test_invite_dont_created_without_auth(): void
    {
        $author = User::factory()->create();
        $target = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $arguments = [
            'status' => InviteStatus::PENDING->value,
            'author_id' => $author->id,
            'target_id' => $target->id,
            'reservation_id' => $reservation->id,
        ];

        $response = $this->call(
            'GET',
            '/api/V1/invite/create',
            $arguments,
        );

        $response->assertStatus(302);
    }

    public function test_invite_dont_set_status_without_auth(): void
    {
        $invite = Invite::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/invite/'.$invite->id.'/status/'.InviteStatus::REVOKED->value,
        );

        $response->assertStatus(302);
    }

    public function test_invite_dont_accept_without_auth(): void
    {
        $invite = Invite::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/invite/'.$invite->id.'/accept',
        );

        $response->assertStatus(302);
    }

    public function test_invite_dont_revoke_without_auth(): void
    {
        $invite = Invite::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/invite/'.$invite->id.'/revoke',
        );

        $response->assertStatus(302);
    }

    public function test_invite_ont_showed_without_auth(): void
    {
        $invite = Invite::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/invite/'.$invite->id
        );

        $response->assertStatus(302);
    }

    public function test_invite_dont_soft_delete_without_auth(): void
    {
        $invite = Invite::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/invite/'.$invite->id
        );

        $response->assertStatus(302);
    }
}
