<?php

namespace Tests\Feature\Web;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_feed(): void
    {
        $this->get(route('feed'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_feed(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('feed'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('feed/Index')
                ->has('items')
            );
    }

    public function test_feed_shows_published_posts(): void
    {
        $this->actingAs(User::factory()->create());
        Post::factory()->create(['published_at' => now()->subDay()]);

        $this->get(route('feed'))
            ->assertInertia(fn (Assert $page) => $page->has('items', 1));
    }

    public function test_feed_excludes_posts_with_null_published_at(): void
    {
        $this->actingAs(User::factory()->create());
        Post::factory()->create(['published_at' => null]);

        $this->get(route('feed'))
            ->assertInertia(fn (Assert $page) => $page->has('items', 0));
    }

    public function test_feed_excludes_future_posts(): void
    {
        $this->actingAs(User::factory()->create());
        Post::factory()->create(['published_at' => now()->addDay()]);

        $this->get(route('feed'))
            ->assertInertia(fn (Assert $page) => $page->has('items', 0));
    }

    public function test_feed_items_have_type_field(): void
    {
        $this->actingAs(User::factory()->create());
        Post::factory()->create(['published_at' => now()->subHour()]);

        $this->get(route('feed'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('items.0.type', 'post')
            );
    }
}
