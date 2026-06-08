<?php

namespace Tests\Feature\Web;

use App\Models\Poll;
use App\Models\PollVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedPollVotingTest extends TestCase
{
    use RefreshDatabase;

    private function createPoll(array $pollAttrs = [], int $optionCount = 3): Poll
    {
        return Poll::factory()
            ->withOptions($optionCount)
            ->create(array_merge(['published_at' => now()->subHour()], $pollAttrs));
    }

    // --- guest redirect ---

    public function test_guest_cannot_vote(): void
    {
        $poll = $this->createPoll();

        $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => [$poll->options->first()->id]])
            ->assertUnauthorized();
    }

    // --- authenticated user can vote ---

    public function test_user_can_vote_on_single_choice_poll(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $poll = $this->createPoll();
        $optionId = $poll->options->first()->id;

        $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => [$optionId]])
            ->assertOk()
            ->assertJsonPath('type', 'poll')
            ->assertJsonPath('has_voted', true)
            ->assertJsonPath('user_vote_ids', [$optionId]);
    }

    public function test_user_can_vote_on_multiple_choice_poll(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $poll = $this->createPoll(['allow_multiple' => true], 3);
        $optionIds = $poll->options->take(2)->pluck('id')->all();

        $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => $optionIds])
            ->assertOk()
            ->assertJsonPath('has_voted', true);

        $this->assertDatabaseCount('poll_votes', 2);
    }

    // --- cannot vote twice ---

    public function test_user_cannot_vote_twice(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $poll = $this->createPoll();
        $optionId = $poll->options->first()->id;

        $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => [$optionId]]);

        $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => [$optionId]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('poll');
    }

    // --- single-choice constraints ---

    public function test_single_choice_poll_rejects_multiple_selections(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $poll = $this->createPoll(['allow_multiple' => false], 3);
        $optionIds = $poll->options->take(2)->pluck('id')->all();

        $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => $optionIds])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('options');
    }

    // --- closed poll ---

    public function test_cannot_vote_on_closed_poll(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $poll = $this->createPoll(['closes_at' => now()->subHour()]);
        $optionId = $poll->options->first()->id;

        $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => [$optionId]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('poll');
    }

    // --- results visibility ---

    public function test_vote_counts_are_returned_after_voting(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $poll = $this->createPoll();
        $option = $poll->options->first();

        $response = $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => [$option->id]])
            ->assertOk()
            ->json();

        $votedOption = collect($response['options'])->firstWhere('id', $option->id);
        $this->assertEquals(1, $votedOption['votes_count']);
    }

    public function test_vote_counts_are_zero_before_voting_in_feed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $poll = $this->createPoll();

        $response = $this->getJson(route('feed'))->json();
        $pollItem = collect($response['data'])->firstWhere('type', 'poll');

        foreach ($pollItem['options'] as $option) {
            $this->assertEquals(0, $option['votes_count']);
        }
    }

    public function test_user_vote_ids_are_empty_before_voting(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->createPoll();

        $response = $this->getJson(route('feed'))->json();
        $pollItem = collect($response['data'])->firstWhere('type', 'poll');

        $this->assertEmpty($pollItem['user_vote_ids']);
        $this->assertFalse($pollItem['has_voted']);
    }

    // --- invalid options ---

    public function test_cannot_vote_for_option_of_another_poll(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $poll = $this->createPoll();
        $otherPoll = $this->createPoll();
        $foreignOptionId = $otherPoll->options->first()->id;

        $this->postJson(route('feed.polls.vote', $poll->id), ['option_ids' => [$foreignOptionId]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('options');
    }

    // --- option_ids validation ---

    public function test_vote_requires_option_ids(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $poll = $this->createPoll();

        $this->postJson(route('feed.polls.vote', $poll->id), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('option_ids');
    }
}
