<?php

namespace Tests\Feature\Web;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedPollCreationTest extends TestCase
{
    use RefreshDatabase;

    private array $validPollData = [
        'question' => 'Какой формат мероприятий вам нравится?',
        'description' => 'Короткое описание',
        'allow_multiple' => false,
        'closes_at' => null,
        'options' => ['Онлайн', 'Офлайн', 'Гибридный'],
    ];

    private array $validPostData = [
        'title' => 'Тестовая публикация',
        'content' => '<p>Содержание</p>',
        'published_at' => '2026-01-01 12:00:00',
    ];

    // --- guest redirects ---

    public function test_guest_cannot_create_post(): void
    {
        $this->post(route('feed.posts.store'), $this->validPostData)
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_create_poll(): void
    {
        $this->post(route('feed.polls.store'), $this->validPollData)
            ->assertRedirect(route('login'));
    }

    // --- regular user forbidden ---

    public function test_regular_user_cannot_create_post(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('feed.posts.store'), $this->validPostData)
            ->assertForbidden();
    }

    public function test_regular_user_cannot_create_poll(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('feed.polls.store'), $this->validPollData)
            ->assertForbidden();
    }

    // --- is_editor can create ---

    public function test_editor_can_create_post(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $this->post(route('feed.posts.store'), $this->validPostData)
            ->assertRedirect(route('feed'));

        $this->assertDatabaseHas('posts', ['title' => $this->validPostData['title']]);
    }

    public function test_editor_can_create_poll(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $this->post(route('feed.polls.store'), $this->validPollData)
            ->assertRedirect(route('feed'));

        $this->assertDatabaseHas('polls', ['question' => $this->validPollData['question']]);
        $this->assertDatabaseCount('poll_options', 3);
    }

    // --- is_admin can create ---

    public function test_admin_can_create_post(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->post(route('feed.posts.store'), $this->validPostData)
            ->assertRedirect(route('feed'));

        $this->assertDatabaseHas('posts', ['title' => $this->validPostData['title']]);
    }

    public function test_admin_can_create_poll(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->post(route('feed.polls.store'), $this->validPollData)
            ->assertRedirect(route('feed'));

        $this->assertDatabaseHas('polls', ['question' => $this->validPollData['question']]);
    }

    // --- poll validation ---

    public function test_poll_requires_question(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = $this->validPollData;
        unset($data['question']);

        $this->post(route('feed.polls.store'), $data)
            ->assertSessionHasErrors('question');
    }

    public function test_poll_requires_at_least_two_options(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = array_merge($this->validPollData, ['options' => ['Один вариант']]);

        $this->post(route('feed.polls.store'), $data)
            ->assertSessionHasErrors('options');
    }

    public function test_poll_does_not_allow_more_than_twenty_options(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = array_merge($this->validPollData, ['options' => array_fill(0, 21, 'Вариант')]);

        $this->post(route('feed.polls.store'), $data)
            ->assertSessionHasErrors('options');
    }

    public function test_poll_closes_at_must_be_future(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = array_merge($this->validPollData, ['closes_at' => now()->subDay()->toDateTimeString()]);

        $this->post(route('feed.polls.store'), $data)
            ->assertSessionHasErrors('closes_at');
    }

    public function test_poll_allows_twenty_options(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = array_merge($this->validPollData, ['options' => array_fill(0, 20, 'Вариант')]);

        $this->post(route('feed.polls.store'), $data)
            ->assertRedirect(route('feed'));

        $this->assertDatabaseCount('poll_options', 20);
    }

    public function test_created_poll_appears_in_feed(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $this->post(route('feed.polls.store'), $this->validPollData);

        $this->getJson(route('feed'))
            ->assertJsonPath('data.0.type', 'poll')
            ->assertJsonPath('data.0.question', $this->validPollData['question']);
    }

    public function test_created_post_appears_in_feed(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $this->post(route('feed.posts.store'), $this->validPostData);

        $this->getJson(route('feed'))
            ->assertJsonPath('data.0.type', 'post')
            ->assertJsonPath('data.0.title', $this->validPostData['title']);
    }

    public function test_poll_stores_author_id(): void
    {
        $user = User::factory()->create(['is_editor' => true]);
        $this->actingAs($user);

        $this->post(route('feed.polls.store'), $this->validPollData);

        $this->assertDatabaseHas('polls', [
            'question' => $this->validPollData['question'],
            'author_id' => $user->id,
        ]);
    }

    public function test_poll_multiple_choice_flag_is_stored(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = array_merge($this->validPollData, ['allow_multiple' => true]);

        $this->post(route('feed.polls.store'), $data);

        $this->assertDatabaseHas('polls', ['allow_multiple' => true]);
    }
}
