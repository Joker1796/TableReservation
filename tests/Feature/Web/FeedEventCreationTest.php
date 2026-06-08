<?php

namespace Tests\Feature\Web;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedEventCreationTest extends TestCase
{
    use RefreshDatabase;

    private array $validData = [
        'title' => 'Тестовое событие',
        'short_description' => 'Краткое описание',
        'description' => 'Полное описание',
        'starts_at' => '2027-01-01 18:00:00',
        'ends_at' => '2027-01-01 22:00:00',
    ];

    // --- guest redirects ---

    public function test_guest_cannot_create_event(): void
    {
        $this->post(route('feed.events.store'), $this->validData)
            ->assertRedirect(route('login'));
    }

    // --- regular user forbidden ---

    public function test_regular_user_cannot_create_event(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('feed.events.store'), $this->validData)
            ->assertForbidden();
    }

    // --- is_editor can create ---

    public function test_editor_can_create_event(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $this->post(route('feed.events.store'), $this->validData)
            ->assertRedirect(route('feed'));

        $this->assertDatabaseHas('events', ['title' => $this->validData['title']]);
    }

    // --- is_admin can create ---

    public function test_admin_can_create_event(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->post(route('feed.events.store'), $this->validData)
            ->assertRedirect(route('feed'));

        $this->assertDatabaseHas('events', ['title' => $this->validData['title']]);
    }

    // --- validation ---

    public function test_title_is_required(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = $this->validData;
        unset($data['title']);

        $this->post(route('feed.events.store'), $data)
            ->assertSessionHasErrors('title');
    }

    public function test_starts_at_is_required(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = $this->validData;
        unset($data['starts_at']);

        $this->post(route('feed.events.store'), $data)
            ->assertSessionHasErrors('starts_at');
    }

    public function test_short_description_max_150(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = array_merge($this->validData, ['short_description' => str_repeat('а', 151)]);

        $this->post(route('feed.events.store'), $data)
            ->assertSessionHasErrors('short_description');
    }

    public function test_ends_at_must_be_after_starts_at(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $data = array_merge($this->validData, ['ends_at' => '2026-12-31 00:00:00']);

        $this->post(route('feed.events.store'), $data)
            ->assertSessionHasErrors('ends_at');
    }

    // --- correct data stored ---

    public function test_event_stores_author_id(): void
    {
        $user = User::factory()->create(['is_editor' => true]);
        $this->actingAs($user);

        $this->post(route('feed.events.store'), $this->validData);

        $this->assertDatabaseHas('events', [
            'title' => $this->validData['title'],
            'author_id' => $user->id,
        ]);
    }

    public function test_event_fields_are_stored_correctly(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $this->post(route('feed.events.store'), $this->validData);

        $event = Event::where('title', $this->validData['title'])->first();
        $this->assertNotNull($event);
        $this->assertEquals($this->validData['short_description'], $event->short_description);
        $this->assertEquals($this->validData['description'], $event->description);
    }

    public function test_event_without_optional_fields(): void
    {
        $this->actingAs(User::factory()->create(['is_editor' => true]));

        $this->post(route('feed.events.store'), [
            'title' => 'Минимальное событие',
            'starts_at' => '2027-03-01 18:00:00',
        ])->assertRedirect(route('feed'));

        $this->assertDatabaseHas('events', ['title' => 'Минимальное событие']);
    }
}
