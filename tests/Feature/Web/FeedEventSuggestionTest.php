<?php

namespace Tests\Feature\Web;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FeedEventSuggestionTest extends TestCase
{
    use RefreshDatabase;

    private array $validData = [
        'title' => 'Предложенное событие',
        'description' => 'Описание предлагаемого события',
    ];

    // --- guest redirect ---

    public function test_guest_cannot_suggest_event(): void
    {
        $this->post(route('feed.events.suggest'), $this->validData)
            ->assertRedirect(route('login'));
    }

    // --- regular user can suggest ---

    public function test_regular_user_can_suggest_event(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create());

        $this->post(route('feed.events.suggest'), $this->validData)
            ->assertRedirect(route('feed'));

        $this->assertDatabaseHas('events', [
            'title' => $this->validData['title'],
            'is_suggestion' => true,
        ]);
    }

    public function test_suggestion_has_null_starts_at(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create());

        $this->post(route('feed.events.suggest'), $this->validData);

        $event = Event::where('is_suggestion', true)->first();
        $this->assertNull($event->starts_at);
    }

    public function test_suggestion_stores_author_id(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('feed.events.suggest'), $this->validData);

        $this->assertDatabaseHas('events', [
            'is_suggestion' => true,
            'author_id' => $user->id,
        ]);
    }

    // --- validation ---

    public function test_title_is_required(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('feed.events.suggest'), ['description' => 'Текст'])
            ->assertSessionHasErrors('title');
    }

    public function test_description_is_required(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('feed.events.suggest'), ['title' => 'Заголовок'])
            ->assertSessionHasErrors('description');
    }

    // --- suggestion does not appear in sidebar ---

    public function test_suggestion_does_not_appear_in_upcoming_events(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('feed.events.suggest'), $this->validData);

        $this->get(route('feed'))
            ->assertInertia(fn ($page) => $page->has('upcomingEvents', 0));
    }

    // --- email notification ---

    public function test_email_sent_to_admins(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs(User::factory()->create());

        $this->post(route('feed.events.suggest'), $this->validData);

        Mail::assertQueued(\App\Mail\NewEventSuggestionMail::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }

    public function test_no_email_when_no_admins(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create());

        $this->post(route('feed.events.suggest'), $this->validData);

        Mail::assertNothingQueued();
    }
}
