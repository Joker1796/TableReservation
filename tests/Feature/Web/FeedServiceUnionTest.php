<?php

namespace Tests\Feature\Web;

use App\Models\Poll;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedServiceUnionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_feed_shows_both_posts_and_polls(): void
    {
        Post::factory()->create(['published_at' => now()->subHour()]);
        Poll::factory()->withOptions()->create(['published_at' => now()->subHours(2)]);

        $response = $this->getJson(route('feed'))->json();

        $types = array_column($response['data'], 'type');
        $this->assertContains('post', $types);
        $this->assertContains('poll', $types);
    }

    public function test_feed_items_are_ordered_by_published_at_desc(): void
    {
        $old = Post::factory()->create(['published_at' => now()->subDays(3)]);
        $new = Post::factory()->create(['published_at' => now()->subDay()]);
        Poll::factory()->withOptions()->create(['published_at' => now()->subDays(2)]);

        $response = $this->getJson(route('feed'))->json();

        $this->assertEquals($new->id, $response['data'][0]['id']);
        $this->assertEquals('post', $response['data'][2]['type']);
        $this->assertEquals($old->id, $response['data'][2]['id']);
    }

    public function test_feed_excludes_unpublished_polls(): void
    {
        Poll::factory()->withOptions()->create(['published_at' => null]);

        $response = $this->getJson(route('feed'))->json();
        $this->assertEmpty($response['data']);
    }

    public function test_feed_excludes_future_polls(): void
    {
        Poll::factory()->withOptions()->create(['published_at' => now()->addHour()]);

        $response = $this->getJson(route('feed'))->json();
        $this->assertEmpty($response['data']);
    }

    public function test_cursor_pagination_works_with_mixed_items(): void
    {
        // 10 posts + 10 polls = 20 items total, page size 15
        Post::factory()->count(10)->create(['published_at' => now()->subHour()]);
        Poll::factory()->withOptions()->count(10)->create(['published_at' => now()->subHours(2)]);

        $first = $this->getJson(route('feed'))->json();
        $this->assertCount(15, $first['data']);
        $this->assertNotNull($first['next_cursor']);

        $second = $this->getJson(route('feed').'?cursor='.urlencode($first['next_cursor']))->json();
        $this->assertCount(5, $second['data']);
        $this->assertNull($second['next_cursor']);

        $this->assertCount(20, array_merge($first['data'], $second['data']));
    }

    public function test_poll_feed_item_has_required_fields(): void
    {
        Poll::factory()->withOptions(3)->create(['published_at' => now()->subHour()]);

        $response = $this->getJson(route('feed'))->json();
        $poll = collect($response['data'])->firstWhere('type', 'poll');

        $this->assertNotNull($poll);
        $this->assertArrayHasKey('question', $poll);
        $this->assertArrayHasKey('allow_multiple', $poll);
        $this->assertArrayHasKey('options', $poll);
        $this->assertArrayHasKey('has_voted', $poll);
        $this->assertArrayHasKey('user_vote_ids', $poll);
        $this->assertArrayHasKey('total_votes', $poll);
        $this->assertArrayHasKey('is_open', $poll);
        $this->assertCount(3, $poll['options']);
    }

    public function test_closed_poll_shows_is_open_false(): void
    {
        Poll::factory()->withOptions()->create([
            'published_at' => now()->subHour(),
            'closes_at' => now()->subMinutes(30),
        ]);

        $response = $this->getJson(route('feed'))->json();
        $poll = collect($response['data'])->firstWhere('type', 'poll');

        $this->assertFalse($poll['is_open']);
    }

    public function test_open_poll_shows_is_open_true(): void
    {
        Poll::factory()->withOptions()->create([
            'published_at' => now()->subHour(),
            'closes_at' => now()->addDay(),
        ]);

        $response = $this->getJson(route('feed'))->json();
        $poll = collect($response['data'])->firstWhere('type', 'poll');

        $this->assertTrue($poll['is_open']);
    }

    public function test_vote_counts_are_visible_after_voting(): void
    {
        $poll = Poll::factory()->withOptions(2)->create(['published_at' => now()->subHour()]);
        $option = $poll->options->first();

        $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => [$option->id]]);

        $response = $this->getJson(route('feed'))->json();
        $pollItem = collect($response['data'])->firstWhere('type', 'poll');

        $this->assertTrue($pollItem['has_voted']);
        $votedOption = collect($pollItem['options'])->firstWhere('id', $option->id);
        $this->assertEquals(1, $votedOption['votes_count']);
    }
}
