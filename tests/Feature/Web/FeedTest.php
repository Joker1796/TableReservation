<?php

namespace Tests\Feature\Web;

use App\Models\Event;
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

    public function test_feed_returns_json_when_requested(): void
    {
        $this->actingAs(User::factory()->create());
        Post::factory()->create(['published_at' => now()->subHour()]);

        $this->getJson(route('feed'))
            ->assertOk()
            ->assertJsonStructure(['data', 'next_cursor', 'per_page']);
    }

    public function test_feed_passes_upcoming_and_recent_events_props(): void
    {
        $this->actingAs(User::factory()->create());
        Event::factory()->create(['starts_at' => now()->addDay()]);
        Event::factory()->create(['starts_at' => now()->subDay()]);

        $this->get(route('feed'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('upcomingEvents', 1)
                ->has('recentEvents', 1)
            );
    }

    public function test_feed_limits_upcoming_events_to_five(): void
    {
        $this->actingAs(User::factory()->create());
        Event::factory()->count(8)->create(['starts_at' => now()->addDay()]);

        $this->get(route('feed'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('upcomingEvents', 5)
            );
    }

    public function test_feed_cursor_pagination_loads_next_page(): void
    {
        $this->actingAs(User::factory()->create());
        Post::factory()->count(20)->create(['published_at' => now()->subHour()]);

        $first = $this->getJson(route('feed'))->json();
        $this->assertCount(15, $first['data']);
        $this->assertNotNull($first['next_cursor']);

        $second = $this->getJson(route('feed').'?cursor='.urlencode($first['next_cursor']))->json();
        $this->assertCount(5, $second['data']);
        $this->assertNull($second['next_cursor']);
    }
}
