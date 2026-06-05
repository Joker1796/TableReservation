<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\WorkshopPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminWorkshopTest extends TestCase
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
        $this->get(route('admin.workshop.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_store(): void
    {
        $this->post(route('admin.workshop.store'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_destroy(): void
    {
        $photo = WorkshopPhoto::create(['filename' => 'test.jpg', 'original_name' => 'test.jpg']);
        $this->delete(route('admin.workshop.destroy', $photo->id))
            ->assertRedirect(route('login'));
    }

    // --- non-admin forbidden ---

    public function test_non_admin_is_forbidden_from_index(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.workshop.index'))
            ->assertForbidden();
    }

    public function test_non_admin_is_forbidden_from_store(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post(route('admin.workshop.store'))
            ->assertForbidden();
    }

    public function test_non_admin_is_forbidden_from_destroy(): void
    {
        $photo = WorkshopPhoto::create(['filename' => 'test.jpg', 'original_name' => 'test.jpg']);
        $this->actingAs(User::factory()->create());
        $this->delete(route('admin.workshop.destroy', $photo->id))
            ->assertForbidden();
    }

    // --- admin happy path ---

    public function test_admin_can_view_workshop_index(): void
    {
        $this->admin();
        $this->get(route('admin.workshop.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('photos'));
    }

    public function test_admin_can_upload_photo(): void
    {
        Storage::fake('public');
        $this->admin();

        $file = UploadedFile::fake()->image('miniature.jpg');

        $this->post(route('admin.workshop.store'), ['photos' => [$file]])
            ->assertRedirect(route('admin.workshop.index'));

        $photo = WorkshopPhoto::first();
        $this->assertNotNull($photo);
        $this->assertSame('miniature.jpg', $photo->original_name);
        Storage::disk('public')->assertExists('workshop/'.$photo->filename);
    }

    public function test_admin_can_upload_multiple_photos(): void
    {
        Storage::fake('public');
        $this->admin();

        $files = [
            UploadedFile::fake()->image('a.jpg'),
            UploadedFile::fake()->image('b.jpg'),
            UploadedFile::fake()->image('c.jpg'),
        ];

        $this->post(route('admin.workshop.store'), ['photos' => $files])
            ->assertRedirect(route('admin.workshop.index'));

        $this->assertSame(3, WorkshopPhoto::count());
    }

    public function test_admin_can_delete_photo(): void
    {
        Storage::fake('public');
        $this->admin();

        Storage::disk('public')->put('workshop/test.jpg', 'fake-image-content');
        $photo = WorkshopPhoto::create(['filename' => 'test.jpg', 'original_name' => 'test.jpg']);

        $this->delete(route('admin.workshop.destroy', $photo->id))
            ->assertRedirect(route('admin.workshop.index'));

        $this->assertDatabaseMissing('workshop_photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing('workshop/test.jpg');
    }

    public function test_upload_requires_image_file(): void
    {
        $this->admin();

        $this->post(route('admin.workshop.store'), [])
            ->assertSessionHasErrors(['photos']);
    }

    public function test_upload_rejects_too_large_file(): void
    {
        Storage::fake('public');
        $this->admin();

        $file = UploadedFile::fake()->image('large.jpg')->size(6000);

        $this->post(route('admin.workshop.store'), ['photos' => [$file]])
            ->assertSessionHasErrors(['photos.0']);
    }

    public function test_index_passes_photos_to_inertia(): void
    {
        $this->admin();
        WorkshopPhoto::create(['filename' => 'a.jpg', 'original_name' => 'a.jpg']);
        WorkshopPhoto::create(['filename' => 'b.jpg', 'original_name' => 'b.jpg']);

        $this->get(route('admin.workshop.index'))
            ->assertInertia(fn (Assert $page) => $page->has('photos', 2));
    }
}
