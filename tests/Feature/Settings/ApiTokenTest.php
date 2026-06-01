<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_token_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('api-token.edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('settings/Token')
                ->where('token', null),
            );
    }

    public function test_api_token_page_requires_authentication(): void
    {
        $this->get(route('api-token.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_api_token_can_be_generated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('api-token.generate'))
            ->assertRedirect(route('api-token.edit'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'api-token',
        ]);
    }

    public function test_generated_token_is_shown_after_redirect(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('api-token.generate'))
            ->assertRedirect(route('api-token.edit'));

        $this->actingAs($user)
            ->followingRedirects()
            ->post(route('api-token.generate'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('settings/Token')
                ->whereNot('token', null),
            );
    }

    public function test_generating_token_replaces_existing_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('old-token');

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->actingAs($user)
            ->post(route('api-token.generate'));

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'api-token',
        ]);
    }

    public function test_generating_token_requires_authentication(): void
    {
        $this->post(route('api-token.generate'))
            ->assertRedirect(route('login'));
    }
}
