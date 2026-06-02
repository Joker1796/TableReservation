<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingRequestWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_store(): void
    {
        $this->post(route('booking-requests.store'), [
            'date' => '2026-01-01',
            'hours' => 2,
        ])
            ->assertRedirect(route('login'));
    }

    public function test_user_can_create_booking_request(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('booking-requests.store'), [
            'date' => '2026-01-01',
            'hours' => 2,
            'comment' => 'test comment',
        ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('booking_requests', ['comment' => 'test comment']);
    }

    public function test_author_is_automatically_attached_as_participant(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('booking-requests.store'), [
            'date' => '2026-01-01',
        ]);

        $br = $user->bookingRequests()->first();
        $this->assertNotNull($br);
        $this->assertTrue($br->users->contains($user));
    }

    public function test_booking_request_not_created_without_date(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('booking-requests.store'), [
            'comment' => 'no date here',
        ])
            ->assertSessionHasErrors(['date']);
    }
}
