<?php

namespace Tests\Feature\Admin;

use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTableTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        return $admin;
    }

    // --- guest redirects ---

    public function test_guests_are_redirected_from_index(): void
    {
        $this->get(route('admin.tables.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_store(): void
    {
        $this->post(route('admin.tables.store'), Table::factory()::ARGUMENTS)
            ->assertRedirect(route('login'));
    }

    // --- non-admin forbidden ---

    public function test_non_admin_is_forbidden_from_index(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.tables.index'))
            ->assertForbidden();
    }

    public function test_non_admin_is_forbidden_from_store(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post(route('admin.tables.store'), Table::factory()::ARGUMENTS)
            ->assertForbidden();
    }

    // --- admin happy path ---

    public function test_admin_can_view_tables_index(): void
    {
        $this->admin();
        $this->get(route('admin.tables.index'))
            ->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->admin();
        $this->get(route('admin.tables.create'))
            ->assertOk();
    }

    public function test_admin_can_create_table(): void
    {
        $this->admin();

        $this->post(route('admin.tables.store'), Table::factory()::ARGUMENTS)
            ->assertRedirect(route('admin.tables.index'));

        $this->assertDatabaseHas('tables', ['name' => 'test table name']);
    }

    public function test_admin_can_view_edit_form(): void
    {
        $this->admin();
        $table = Table::factory()->create();

        $this->get(route('admin.tables.edit', $table->id))
            ->assertOk();
    }

    public function test_admin_can_update_table(): void
    {
        $this->admin();
        $table = Table::factory()->create();

        $this->put(route('admin.tables.update', $table->id), Table::factory()::UPDATED_ARGUMENTS)
            ->assertRedirect(route('admin.tables.index'));

        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'name' => 'test table name updated',
        ]);
    }

    public function test_admin_can_delete_table(): void
    {
        $this->admin();
        $table = Table::factory()->create();

        $this->delete(route('admin.tables.destroy', $table->id))
            ->assertRedirect(route('admin.tables.index'));

        $this->assertSoftDeleted('tables', ['id' => $table->id]);
    }

    public function test_table_not_created_without_name(): void
    {
        $this->admin();

        $data = Table::factory()::ARGUMENTS;
        unset($data['name']);

        $this->post(route('admin.tables.store'), $data)
            ->assertSessionHasErrors(['name']);
    }
}
