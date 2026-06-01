<?php

namespace Tests\Feature\Web;

use App\Enums\InviteStatus;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_accept(): void
    {
        $invite = Invite::factory()->create();

        $this->put(route('invites.accept', $invite->id))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_reject(): void
    {
        $invite = Invite::factory()->create();

        $this->put(route('invites.reject', $invite->id))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_accept_own_invite(): void
    {
        $user = User::factory()->create();
        $invite = Invite::factory()->create([
            'target_id' => $user->id,
            'status' => InviteStatus::PENDING,
        ]);

        $this->actingAs($user)
            ->put(route('invites.accept', $invite->id))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('invites', [
            'id' => $invite->id,
            'status' => InviteStatus::ACCEPTED->value,
        ]);
    }

    public function test_user_can_reject_own_invite(): void
    {
        $user = User::factory()->create();
        $invite = Invite::factory()->create([
            'target_id' => $user->id,
            'status' => InviteStatus::PENDING,
        ]);

        $this->actingAs($user)
            ->put(route('invites.reject', $invite->id))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('invites', [
            'id' => $invite->id,
            'status' => InviteStatus::REVOKED->value,
        ]);
    }

    public function test_user_cannot_accept_others_invite(): void
    {
        $invite = Invite::factory()->create(['status' => InviteStatus::PENDING]);

        $this->actingAs(User::factory()->create())
            ->put(route('invites.accept', $invite->id))
            ->assertNotFound();
    }

    public function test_user_cannot_reject_others_invite(): void
    {
        $invite = Invite::factory()->create(['status' => InviteStatus::PENDING]);

        $this->actingAs(User::factory()->create())
            ->put(route('invites.reject', $invite->id))
            ->assertNotFound();
    }
}
