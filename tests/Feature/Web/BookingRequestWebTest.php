<?php

namespace Tests\Feature\Web;

use App\Mail\NewBookingRequestMail;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    public function test_user_can_specify_table_in_booking_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $table = Table::factory()->create();

        $this->post(route('booking-requests.store'), [
            'date' => '2026-01-01',
            'table_id' => $table->id,
        ]);

        $this->assertDatabaseHas('booking_requests', ['table_id' => $table->id]);
    }

    public function test_user_can_add_participants_to_booking_request(): void
    {
        $author = User::factory()->create();
        $participant = User::factory()->create();
        $this->actingAs($author);

        $this->post(route('booking-requests.store'), [
            'date' => '2026-01-01',
            'user_ids' => [$participant->id],
        ]);

        $br = $author->bookingRequests()->first();
        $this->assertTrue($br->users->contains($participant));
        $this->assertTrue($br->users->contains($author));
    }

    public function test_invalid_table_id_fails_validation(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('booking-requests.store'), [
            'date' => '2026-01-01',
            'table_id' => 99999,
        ])
            ->assertSessionHasErrors(['table_id']);
    }

    public function test_invalid_user_id_in_user_ids_fails_validation(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('booking-requests.store'), [
            'date' => '2026-01-01',
            'user_ids' => [99999],
        ])
            ->assertSessionHasErrors(['user_ids.0']);
    }

    public function test_email_is_queued_to_all_admins_on_booking_request_creation(): void
    {
        Mail::fake();

        $admin1 = User::factory()->create(['is_admin' => true]);
        $admin2 = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user);

        $this->post(route('booking-requests.store'), ['date' => '2026-01-01']);

        Mail::assertQueued(NewBookingRequestMail::class, 2);
        Mail::assertQueued(NewBookingRequestMail::class, fn ($mail) => $mail->hasTo($admin1->email));
        Mail::assertQueued(NewBookingRequestMail::class, fn ($mail) => $mail->hasTo($admin2->email));
    }

    public function test_email_is_not_sent_to_regular_users(): void
    {
        Mail::fake();

        User::factory()->create(['is_admin' => true]);
        $regularUser = User::factory()->create(['is_admin' => false]);

        $this->actingAs($regularUser);

        $this->post(route('booking-requests.store'), ['date' => '2026-01-01']);

        Mail::assertQueued(NewBookingRequestMail::class, fn ($mail) => ! $mail->hasTo($regularUser->email));
    }

    public function test_no_email_queued_when_no_admins_exist(): void
    {
        Mail::fake();

        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->post(route('booking-requests.store'), ['date' => '2026-01-01']);

        Mail::assertNothingQueued();
    }

    public function test_queued_email_contains_correct_booking_request(): void
    {
        Mail::fake();

        User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create();

        $this->actingAs($author);

        $this->post(route('booking-requests.store'), [
            'date' => '2026-06-15',
            'comment' => 'тестовый комментарий',
        ]);

        Mail::assertQueued(NewBookingRequestMail::class, function ($mail) {
            return $mail->bookingRequest->comment === 'тестовый комментарий'
                && str_starts_with($mail->bookingRequest->date, '2026-06-15');
        });
    }
}
