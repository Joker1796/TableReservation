<?php

namespace Tests\Feature\Admin;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminPostTest extends TestCase
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
        $this->get(route('admin.posts.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_create(): void
    {
        $this->get(route('admin.posts.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_store(): void
    {
        $this->post(route('admin.posts.store'), Post::factory()::ARGUMENTS)
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_destroy(): void
    {
        $post = Post::factory()->create();
        $this->delete(route('admin.posts.destroy', $post->id))
            ->assertRedirect(route('login'));
    }

    // --- non-admin forbidden ---

    public function test_non_admin_is_forbidden_from_index(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.posts.index'))
            ->assertForbidden();
    }

    public function test_non_admin_is_forbidden_from_store(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post(route('admin.posts.store'), Post::factory()::ARGUMENTS)
            ->assertForbidden();
    }

    public function test_non_admin_is_forbidden_from_destroy(): void
    {
        $post = Post::factory()->create();
        $this->actingAs(User::factory()->create());
        $this->delete(route('admin.posts.destroy', $post->id))
            ->assertForbidden();
    }

    // --- admin happy path ---

    public function test_admin_can_view_posts_index(): void
    {
        $this->admin();
        $this->get(route('admin.posts.index'))
            ->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->admin();
        $this->get(route('admin.posts.create'))
            ->assertOk();
    }

    public function test_admin_can_create_post(): void
    {
        $this->admin();

        $this->post(route('admin.posts.store'), Post::factory()::ARGUMENTS)
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', ['title' => 'Test post title']);
    }

    public function test_admin_can_view_edit_form(): void
    {
        $this->admin();
        $post = Post::factory()->create();

        $this->get(route('admin.posts.edit', $post->id))
            ->assertOk();
    }

    public function test_admin_can_update_post(): void
    {
        $this->admin();
        $post = Post::factory()->create();

        $this->put(route('admin.posts.update', $post->id), Post::factory()::UPDATED_ARGUMENTS)
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated post title',
        ]);
    }

    public function test_admin_can_delete_post(): void
    {
        $this->admin();
        $post = Post::factory()->create();

        $this->delete(route('admin.posts.destroy', $post->id))
            ->assertRedirect(route('admin.posts.index'));

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_post_not_created_without_title(): void
    {
        $this->admin();

        $data = Post::factory()::ARGUMENTS;
        unset($data['title']);

        $this->post(route('admin.posts.store'), $data)
            ->assertSessionHasErrors(['title']);
    }

    public function test_post_not_created_without_content(): void
    {
        $this->admin();

        $data = Post::factory()::ARGUMENTS;
        unset($data['content']);

        $this->post(route('admin.posts.store'), $data)
            ->assertSessionHasErrors(['content']);
    }

    public function test_index_paginates_posts(): void
    {
        $this->admin();
        Post::factory()->count(20)->create();

        $this->get(route('admin.posts.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('posts.data', 15)
                ->where('posts.total', 20)
                ->where('posts.current_page', 1)
            );
    }
}
