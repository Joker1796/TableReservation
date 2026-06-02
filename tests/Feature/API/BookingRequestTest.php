<?php

namespace Feature\API;

use App\Models\BookingRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function acting(): User
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get('/');

        return $user;
    }

    public function test_booking_request_created_successfully(): void
    {
        $user = $this->acting();

        $response = $this->call(
            'GET',
            '/api/V1/booking-request/create',
            BookingRequest::factory()::ARGUMENTS,
        );

        $response->assertOk();

        $this->assertArrayHasKey('author_id', $response->json());
        $this->assertEquals($user->id, $response->json('author_id'));
    }

    public function test_booking_request_created_with_table_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $arguments = BookingRequest::factory()::ARGUMENTS;
        $arguments['table_id'] = $table->id;

        $response = $this->call(
            'GET',
            '/api/V1/booking-request/create',
            $arguments
        );

        $response->assertOk();

        $this->assertArrayHasKey('table_id', $response->json());
        $this->assertEquals($table->id, $response->json('table_id'));
    }

    public function test_booking_request_created_with_author_successfully(): void
    {
        $this->acting();

        $user = User::factory()->create();

        $arguments = BookingRequest::factory()::ARGUMENTS;
        $arguments['author'] = $user->id;

        $response = $this->call(
            'GET',
            '/api/V1/booking-request/create',
            $arguments
        );

        $response->assertOk();

        $this->assertArrayHasKey('author_id', $response->json());
        $this->assertEquals($user->id, $response->json('author_id'));
    }

    public function test_booking_request_created_with_users_successfully(): void
    {
        $this->acting();

        $users = User::factory()
            ->count(2)
            ->create();

        $arguments = BookingRequest::factory()::ARGUMENTS;
        $arguments['user_ids'] = $users->pluck('id')->toArray();

        $response = $this->call(
            'GET',
            '/api/V1/booking-request/create',
            $arguments
        );

        $response->assertOk();
    }

    public function test_booking_request_updated_successfully(): void
    {
        $this->acting();

        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/booking-request/'.$bookingRequest->id,
            BookingRequest::factory()::UPDATED_ARGUMENTS,
        );

        $response->assertOk();
        $response->assertJsonFragment(BookingRequest::factory()::UPDATED_ARGUMENTS);
    }

    public function test_booking_request_showed_successfully(): void
    {
        $this->acting();

        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'GET',
            '/api/V1/booking-request/'.$bookingRequest->id
        );

        $response->assertOk();

        $this->assertEquals($bookingRequest->id, $response->json('id'));
    }

    public function test_booking_request_soft_delete_successfully(): void
    {
        $this->acting();

        $bookingRequest = BookingRequest::factory()->create();

        $response = $this->call(
            'DELETE',
            '/api/V1/booking-request/'.$bookingRequest->id
        );

        $response->assertOk();

        $this->assertArrayHasKey('deleted_at', $response->json());
        $this->assertNotNull($response->json('deleted_at'));
    }

    public function test_booking_request_attach_user_successfully(): void
    {
        $this->acting();

        $bookingRequest = BookingRequest::factory()->create();
        $user = User::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/booking-request/'.$bookingRequest->id.'/user/'.$user->id
        );

        $response->assertOk();

        $this->assertDatabaseHas('booking_request_user', [
            'user_id' => $user->id,
            'booking_request_id' => $bookingRequest->id,
        ]);
    }

    public function test_booking_request_detach_user_successfully(): void
    {
        $this->acting();

        $bookingRequest = BookingRequest::factory()
            ->has(User::factory(), 'users')
            ->create();

        $userId = $bookingRequest->users()->first()->id;

        $response = $this->call(
            'DELETE',
            '/api/V1/booking-request/'.$bookingRequest->id.'/user/'.$userId
        );

        $response->assertOk();

        $this->assertDatabaseMissing('booking_request_user', [
            'user_id' => $userId,
            'booking_request_id' => $bookingRequest->id,
        ]);
    }

    public function test_booking_request_associate_table_successfully(): void
    {
        $this->acting();

        $bookingRequest = BookingRequest::factory()->create();
        $table = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/booking-request/'.$bookingRequest->id.'/table/'.$table->id
        );

        $response->assertOk();

        $this->assertArrayHasKey('table_id', $response->json());
        $this->assertEquals($response->json('table_id'), $table->id);
        $this->assertArrayHasKey('table', $response->json());
        $this->assertNotNull($response->json('table'));
        $this->assertEquals($response->json('table.id'), $table->id);
    }

    public function test_booking_request_delete_table_successfully(): void
    {
        $this->acting();

        $bookingRequest = BookingRequest::factory()->create();

        $this->assertNotNull($bookingRequest->table()->first()->id);

        $response = $this->call(
            'DELETE',
            '/api/V1/booking-request/'.$bookingRequest->id.'/table',
        );

        $response->assertOk();

        $this->assertArrayHasKey('table_id', $response->json());
        $this->assertNull($response->json('table_id'));
        $this->assertArrayHasKey('table', $response->json());
        $this->assertNull($response->json('table'));
    }

    public function test_booking_request_dont_created_without_required_date(): void
    {
        $this->acting();

        $arguments = BookingRequest::factory()::ARGUMENTS;
        unset($arguments['date']);

        $response = $this->call('GET', '/api/V1/booking-request/create', $arguments);

        $response->assertStatus(302);
    }

    public function test_booking_request_dont_created_with_invalid_hours(): void
    {
        $this->acting();

        $arguments = BookingRequest::factory()::ARGUMENTS;
        $arguments['hours'] = -1;

        $response = $this->call('GET', '/api/V1/booking-request/create', $arguments);

        $response->assertStatus(302);
    }

    public function test_booking_request_dont_created_with_hours_over_max(): void
    {
        $this->acting();

        $arguments = BookingRequest::factory()::ARGUMENTS;
        $arguments['hours'] = 13;

        $response = $this->call('GET', '/api/V1/booking-request/create', $arguments);

        $response->assertStatus(302);
    }

    public function test_booking_request_created_with_zero_hours_successfully(): void
    {
        $this->acting();

        $arguments = BookingRequest::factory()::ARGUMENTS;
        $arguments['hours'] = 0;

        $response = $this->call('GET', '/api/V1/booking-request/create', $arguments);

        $response->assertOk();
        $this->assertEquals(0, $response->json('hours'));
    }

    public function test_booking_request_created_with_null_hours_successfully(): void
    {
        $this->acting();

        $arguments = BookingRequest::factory()::ARGUMENTS;
        $arguments['hours'] = null;

        $response = $this->call('GET', '/api/V1/booking-request/create', $arguments);

        $response->assertOk();
        $this->assertNull($response->json('hours'));
    }

    public function test_booking_request_dont_created_with_nonexistent_table_id(): void
    {
        $this->acting();

        $arguments = BookingRequest::factory()::ARGUMENTS;
        $arguments['table_id'] = 99999;

        $response = $this->call('GET', '/api/V1/booking-request/create', $arguments);

        $response->assertStatus(302);
    }

    public function test_booking_request_show_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('GET', '/api/V1/booking-request/99999');

        $response->assertNotFound();
    }

    public function test_booking_request_update_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('PUT', '/api/V1/booking-request/99999', BookingRequest::factory()::UPDATED_ARGUMENTS);

        $response->assertNotFound();
    }

    public function test_booking_request_soft_delete_returns_404_for_nonexistent(): void
    {
        $this->acting();

        $response = $this->call('DELETE', '/api/V1/booking-request/99999');

        $response->assertNotFound();
    }
}
