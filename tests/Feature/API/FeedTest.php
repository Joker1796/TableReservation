<?php

namespace Tests\Feature\API;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    private function acting(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['banned' => false])
            ->get('/');
    }

    public function test_guest_cannot_access_feed(): void
    {
        $response = $this->getJson('/api/V1/feed');
        $response->assertUnauthorized();
    }

    public function test_authenticated_user_gets_feed(): void
    {
        $this->acting();

        $response = $this->getJson('/api/V1/feed');

        $response->assertOk();
        $response->assertJsonStructure(['data', 'next_cursor', 'next_page_url']);
    }

    public function test_feed_returns_published_posts(): void
    {
        $this->acting();
        Post::factory()->create(['published_at' => now()->subHour()]);

        $response = $this->getJson('/api/V1/feed');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_feed_excludes_null_published_at(): void
    {
        $this->acting();
        Post::factory()->create(['published_at' => null]);

        $response = $this->getJson('/api/V1/feed');

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }

    public function test_feed_excludes_future_posts(): void
    {
        $this->acting();
        Post::factory()->create(['published_at' => now()->addDay()]);

        $response = $this->getJson('/api/V1/feed');

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }

    public function test_feed_items_have_type_field(): void
    {
        $this->acting();
        Post::factory()->create(['published_at' => now()->subHour()]);

        $response = $this->getJson('/api/V1/feed');

        $response->assertOk();
        $this->assertSame('post', $response->json('data.0.type'));
    }

    public function test_feed_cursor_pagination(): void
    {
        $this->acting();
        Post::factory()->count(20)->create(['published_at' => now()->subHour()]);

        $first = $this->getJson('/api/V1/feed');
        $first->assertOk();
        $this->assertCount(15, $first->json('data'));

        $cursor = $first->json('next_cursor');
        $this->assertNotNull($cursor);

        $second = $this->getJson('/api/V1/feed?cursor='.urlencode($cursor));
        $second->assertOk();
        $this->assertCount(5, $second->json('data'));
        $this->assertNull($second->json('next_cursor'));
    }
}
