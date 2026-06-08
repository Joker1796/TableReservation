<?php

namespace Tests\Feature\Web;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FeedSuggestionTest extends TestCase
{
    use RefreshDatabase;

    private array $validData = [
        'title' => 'Предложение от пользователя',
        'content' => 'Описание предлагаемой новости',
    ];

    // --- guest redirect ---

    public function test_guest_cannot_submit_suggestion(): void
    {
        $this->post(route('feed.suggest'), $this->validData)
            ->assertRedirect(route('login'));
    }

    // --- no contacts redirect ---

    public function test_user_without_contacts_cannot_suggest_post(): void
    {
        $this->actingAs(User::factory()->create(['phone' => null, 'contacts' => null]));

        $this->post(route('feed.suggest'), $this->validData)
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseEmpty('posts');
    }

    // --- regular user can suggest ---

    public function test_regular_user_can_submit_suggestion(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['phone' => '79001234567']));

        $this->post(route('feed.suggest'), $this->validData)
            ->assertRedirect(route('feed'));

        $this->assertDatabaseHas('posts', [
            'title' => $this->validData['title'],
            'is_suggestion' => true,
        ]);
    }

    public function test_suggestion_has_null_published_at(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['phone' => '79001234567']));

        $this->post(route('feed.suggest'), $this->validData);

        $post = Post::where('is_suggestion', true)->first();
        $this->assertNull($post->published_at);
    }

    public function test_suggestion_stores_author_id(): void
    {
        Mail::fake();
        $user = User::factory()->create(['phone' => '79001234567']);
        $this->actingAs($user);

        $this->post(route('feed.suggest'), $this->validData);

        $this->assertDatabaseHas('posts', [
            'is_suggestion' => true,
            'author_id' => $user->id,
        ]);
    }

    // --- validation ---

    public function test_title_is_required(): void
    {
        $this->actingAs(User::factory()->create(['phone' => '79001234567']));

        $this->post(route('feed.suggest'), ['content' => 'Текст'])
            ->assertSessionHasErrors('title');
    }

    public function test_content_is_required(): void
    {
        $this->actingAs(User::factory()->create(['phone' => '79001234567']));

        $this->post(route('feed.suggest'), ['title' => 'Заголовок'])
            ->assertSessionHasErrors('content');
    }

    public function test_content_max_length_is_5000(): void
    {
        $this->actingAs(User::factory()->create(['phone' => '79001234567']));

        $this->post(route('feed.suggest'), [
            'title' => 'Заголовок',
            'content' => str_repeat('а', 5001),
        ])->assertSessionHasErrors('content');
    }

    // --- suggestion does not appear in feed ---

    public function test_suggestion_does_not_appear_in_feed(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['phone' => '79001234567']));

        $this->post(route('feed.suggest'), $this->validData);

        $this->getJson(route('feed'))
            ->assertJsonPath('data', []);
    }

    // --- email notification ---

    public function test_email_is_sent_to_admins(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs(User::factory()->create(['phone' => '79001234567']));

        $this->post(route('feed.suggest'), $this->validData);

        Mail::assertQueued(\App\Mail\NewPostSuggestionMail::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }

    public function test_no_email_sent_when_no_admins(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['phone' => '79001234567']));

        $this->post(route('feed.suggest'), $this->validData);

        Mail::assertNothingQueued();
    }

    // --- is_editor and is_admin can also suggest ---

    public function test_editor_can_also_submit_suggestion(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['is_editor' => true, 'phone' => '79001234567']));

        $this->post(route('feed.suggest'), $this->validData)
            ->assertRedirect(route('feed'));
    }

    public function test_admin_can_also_submit_suggestion(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['is_admin' => true, 'phone' => '79001234567']));

        $this->post(route('feed.suggest'), $this->validData)
            ->assertRedirect(route('feed'));
    }
}
