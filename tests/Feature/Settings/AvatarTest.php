<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_upload_avatar(): void
    {
        Storage::fake('public');

        $this->post('/settings/avatar', ['avatar' => UploadedFile::fake()->image('photo.jpg')])
            ->assertRedirect('/login');
    }

    public function test_guest_cannot_delete_avatar(): void
    {
        $this->delete('/settings/avatar')
            ->assertRedirect('/login');
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/settings/avatar', ['avatar' => UploadedFile::fake()->image('photo.jpg')])
            ->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->getRawOriginal('avatar'));
        Storage::disk('public')->assertExists($user->getRawOriginal('avatar'));
    }

    public function test_old_avatar_is_deleted_on_new_upload(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/settings/avatar', ['avatar' => UploadedFile::fake()->image('first.jpg')]);
        $user->refresh();
        $firstPath = $user->getRawOriginal('avatar');

        $this->post('/settings/avatar', ['avatar' => UploadedFile::fake()->image('second.jpg')]);
        $user->refresh();

        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($user->getRawOriginal('avatar'));
    }

    public function test_user_can_delete_avatar(): void
    {
        Storage::fake('public');

        $path = UploadedFile::fake()->image('photo.jpg')->store('avatars', 'public');
        $user = User::factory()->create(['avatar' => $path]);
        $this->actingAs($user);

        $this->delete('/settings/avatar')->assertRedirect();

        $user->refresh();
        $this->assertNull($user->getRawOriginal('avatar'));
        Storage::disk('public')->assertMissing($path);
    }

    public function test_avatar_validation_rejects_non_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/settings/avatar', ['avatar' => UploadedFile::fake()->create('doc.pdf', 100)])
            ->assertSessionHasErrors('avatar');
    }

    public function test_avatar_validation_rejects_oversized_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/settings/avatar', ['avatar' => UploadedFile::fake()->image('big.jpg')->size(3000)])
            ->assertSessionHasErrors('avatar');
    }

    public function test_avatar_url_is_exposed_via_model(): void
    {
        Storage::fake('public');

        $path = UploadedFile::fake()->image('photo.jpg')->store('avatars', 'public');
        $user = User::factory()->create(['avatar' => $path]);

        $this->assertStringContainsString('storage/avatars', $user->avatar);
    }
}
