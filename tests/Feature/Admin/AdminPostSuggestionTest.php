<?php

namespace Tests\Feature\Admin;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostSuggestionTest extends TestCase
{
    use RefreshDatabase;

    private function suggestion(array $attrs = []): Post
    {
        return Post::factory()->create(array_merge([
            'is_suggestion' => true,
            'published_at' => null,
        ], $attrs));
    }

    // --- access control ---

    public function test_guest_cannot_approve(): void
    {
        $post = $this->suggestion();

        $this->put(route('admin.posts.approve', $post->id))
            ->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_approve(): void
    {
        $this->actingAs(User::factory()->create());
        $post = $this->suggestion();

        $this->put(route('admin.posts.approve', $post->id))
            ->assertForbidden();
    }

    public function test_editor_cannot_approve(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));
        $post = $this->suggestion();

        $this->put(route('admin.posts.approve', $post->id))
            ->assertForbidden();
    }

    // --- approve ---

    public function test_admin_can_approve_suggestion(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $post = $this->suggestion();

        $this->put(route('admin.posts.approve', $post->id))
            ->assertRedirect(route('admin.posts.index'));

        $post->refresh();
        $this->assertNotNull($post->published_at);
        $this->assertTrue($post->is_suggestion);
    }

    public function test_approve_sets_published_at_to_now(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $post = $this->suggestion();

        $this->put(route('admin.posts.approve', $post->id));

        $post->refresh();
        $this->assertNotNull($post->published_at);
        $this->assertTrue($post->published_at->diffInSeconds(now()) < 5);
    }

    public function test_approved_suggestion_appears_in_feed(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);
        $post = $this->suggestion();

        $this->put(route('admin.posts.approve', $post->id));

        $this->getJson(route('feed'))
            ->assertJsonPath('data.0.id', $post->id)
            ->assertJsonPath('data.0.type', 'post');
    }

    public function test_cannot_approve_non_suggestion_post(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $post = Post::factory()->create(['is_suggestion' => false]);

        $this->put(route('admin.posts.approve', $post->id))
            ->assertNotFound();
    }

    // --- pending suggestions in shared props ---

    public function test_pending_suggestions_are_shared_for_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);
        $this->suggestion();

        $this->get(route('feed'))
            ->assertInertia(fn ($page) => $page->has('pendingPostSuggestions', 1));
    }

    public function test_approved_suggestion_not_in_pending_shared_props(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);
        $this->suggestion(['published_at' => now()]);

        $this->get(route('feed'))
            ->assertInertia(fn ($page) => $page->has('pendingPostSuggestions', 0));
    }

    public function test_pending_suggestions_not_shared_for_regular_user(): void
    {
        $this->actingAs(User::factory()->create());
        $this->suggestion();

        $this->get(route('feed'))
            ->assertInertia(fn ($page) => $page->where('pendingPostSuggestions', []));
    }

    // --- admin posts index shows suggestions ---

    public function test_admin_index_includes_suggestions(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->suggestion(['title' => 'Предложенная новость']);

        $this->get(route('admin.posts.index'))
            ->assertInertia(fn ($page) => $page
                ->has('posts.data', 1)
                ->where('posts.data.0.is_suggestion', true)
            );
    }
}
