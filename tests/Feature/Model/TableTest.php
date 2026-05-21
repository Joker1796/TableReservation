<?php

namespace Feature\Model;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableTest extends TestCase
{
    use RefreshDatabase;

    const array BASE_ATTRIBUTES = [
        'name' => 'test table name',
        'description' => 'test description',
        'status' => '1',
    ];

    const array UPDATED_BASIC_ATTRIBUTES = [
        'name' => 'test table name updated',
        'description' => 'test description updated',
        'status' => '0',
    ];

    public function acting(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['banned' => false])
            ->get('/');
    }

    public function test_table_created_successfully(): void
    {
        $this->acting();

        $response = $this->call('GET', '/api/V1/table/create', self::BASE_ATTRIBUTES);

        $response->assertOk();
    }

    public function test_table_updated_successfully(): void
    {
        $this->acting();

        $weapon = Table::factory()->create();

        $response = $this->call(
            'PUT',
            '/api/V1/table/'.$weapon->id, self::UPDATED_BASIC_ATTRIBUTES
        );

        $response->assertOk();

        $response->assertJsonFragment(self::UPDATED_BASIC_ATTRIBUTES);
    }

    public function test_table_showed_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $response = $this->call('GET', '/api/V1/table/'.$table->id);

        $response->assertOk();
    }

    public function test_table_soft_delete_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();

        $response = $this->call('DELETE', '/api/V1/table/'.$table->id);

        $response->assertOk();

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('deleted_at', $content);
        $this->assertNotNull($content['deleted_at']);
    }

    public function test_table_add_reservation_successfully(): void
    {
        $this->acting();

        $table = Table::factory()->create();
        $reservation = Reservation::factory()->create();

        $responseDetach = $this->call(
            'PUT',
            '/api/V1/table/'.$table->id.'/reservation/'.$reservation->id
        );

        $responseDetach->assertOk();
    }

    public function test_table_delete_reservation_successfully(): void
    {
        $this->acting();

        $table = Table::factory()
            ->has(Reservation::factory())
            ->create();

        $responseDetach = $this->call(
            'DELETE',
            '/api/V1/table/'.$table->id.'/reservation/'.$table->reservations()->first()->id
        );

        $responseDetach->assertOk();
    }
}
