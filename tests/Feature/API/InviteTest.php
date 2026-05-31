<?php

namespace Feature\API;

use App\Enums\InviteStatus;
use App\Models\Invite;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteTest extends TestCase
{
    use RefreshDatabase;

    public function acting(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['banned' => false])
            ->get('/');
    }

    public function test_invite_created_successfully(): void
    {
        $this->acting();

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

        $response->assertOk();

        $this->assertEquals($response->json()['author_id'], $author->id);
        $this->assertEquals($response->json()['target_id'], $target->id);
        $this->assertEquals($response->json()['reservation_id'], $reservation->id);
        $this->assertEquals($response->json()['status'], InviteStatus::PENDING->value);
    }

    public function test_invite_created_without_status_successfully(): void
    {
        $this->acting();

        $author = User::factory()->create();
        $target = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $arguments = [
            'author_id' => $author->id,
            'target_id' => $target->id,
            'reservation_id' => $reservation->id,
        ];

        $response = $this->call(
            'GET',
            '/api/V1/invite/create',
            $arguments,
        );

        $response->assertOk();

        $this->assertEquals($response->json()['author_id'], $author->id);
        $this->assertEquals($response->json()['target_id'], $target->id);
        $this->assertEquals($response->json()['reservation_id'], $reservation->id);
        $this->assertEquals($response->json()['status'], InviteStatus::PENDING->value);
    }

    public function test_invite_dont_created_with_incorrect_status(): void
    {
        $this->acting();

        $response = $this->call(
            'GET',
            '/api/V1/invite/create',
            ['status' => 'incorrect']
        );

        $response->assertStatus(302);
    }

    public function test_invite_set_status_successfully(): void
    {
        $this->acting();

        $invite = Invite::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/invite/'.$invite->id.'/status/'.InviteStatus::REVOKED->value,
        );

        $response->assertOk();

        $this->assertEquals($response->json()['status'], InviteStatus::REVOKED->value);
    }

    public function test_invite_accept_successfully(): void
    {
        $this->acting();

        $invite = Invite::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/invite/'.$invite->id.'/accept',
        );

        $response->assertOk();

        $this->assertEquals($response->json()['status'], InviteStatus::ACCEPTED->value);
        $this->assertDatabaseHas('reservation_user', [
            'reservation_id' => $invite->reservation_id,
            'user_id' => $invite->target_id,
        ]);
    }

    public function test_invite_revoke_successfully(): void
    {
        $this->acting();

        $invite = Invite::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/invite/'.$invite->id.'/revoke',
        );

        $response->assertOk();

        $this->assertEquals($response->json()['status'], InviteStatus::REVOKED->value);
        $this->assertDatabaseMissing('reservation_user', [
            'reservation_id' => $invite->reservation_id,
            'user_id' => $invite->target_id,
        ]);
    }

    public function test_invite_showed_successfully(): void
    {
        $this->acting();

        $invite = Invite::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/invite/'.$invite->id
        );

        $response->assertOk();

        $this->assertEquals($invite->id, $response->json('id'));
    }

    public function test_invite_soft_delete_successfully(): void
    {
        $this->acting();

        $invite = Invite::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/invite/'.$invite->id
        );

        $response->assertOk();

        $this->assertNotNull($response->json('deleted_at'));
        $this->assertDatabaseHas('invites', ['id' => $invite->id]);
    }

    public function test_invite_dont_created_without_required_author_id(): void
    {
        $this->acting();

        $target = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $response = $this->call('GET', '/api/V1/invite/create', [
            'target_id' => $target->id,
            'reservation_id' => $reservation->id,
        ]);

        $response->assertStatus(302);
    }

    public function test_invite_dont_created_without_required_target_id(): void
    {
        $this->acting();

        $author = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $response = $this->call('GET', '/api/V1/invite/create', [
            'author_id' => $author->id,
            'reservation_id' => $reservation->id,
        ]);

        $response->assertStatus(302);
    }

    public function test_invite_dont_created_without_required_reservation_id(): void
    {
        $this->acting();

        $author = User::factory()->create();
        $target = User::factory()->create();

        $response = $this->call('GET', '/api/V1/invite/create', [
            'author_id' => $author->id,
            'target_id' => $target->id,
        ]);

        $response->assertStatus(302);
    }

    public function test_invite_show_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('GET', '/api/V1/invite/99999');

        $response->assertNotFound();
    }

    public function test_invite_soft_delete_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('DELETE', '/api/V1/invite/99999');

        $response->assertNotFound();
    }
}
