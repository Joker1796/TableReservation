<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_new_admin_user(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Email', 'ivan@example.com')
            ->expectsQuestion('Имя', 'Иван Иванов')
            ->expectsQuestion('Пароль', 'password123')
            ->expectsOutputToContain('успешно создан')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'is_admin' => true,
        ]);
    }

    public function test_created_admin_has_verified_email(): void
    {
        $this->artisan('admin:create', [
            '--name' => 'Admin',
            '--email' => 'admin@example.com',
            '--password' => 'password',
        ])->assertExitCode(0);

        $user = User::where('email', 'admin@example.com')->first();

        $this->assertNotNull($user->email_verified_at);
    }

    public function test_creates_admin_via_options_without_prompts(): void
    {
        $this->artisan('admin:create', [
            '--name' => 'Admin',
            '--email' => 'admin@example.com',
            '--password' => 'secret123',
        ])
            ->expectsOutputToContain('успешно создан')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);
    }

    public function test_promotes_existing_user_to_admin(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.com', 'is_admin' => false]);

        $this->artisan('admin:create', ['--email' => 'existing@example.com'])
            ->expectsConfirmation('Пользователь с email «existing@example.com» уже существует. Назначить его администратором?', 'yes')
            ->expectsOutputToContain('теперь администратор')
            ->assertExitCode(0);

        $this->assertTrue($user->fresh()->is_admin);
    }

    public function test_does_not_duplicate_existing_user_when_promoted(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->artisan('admin:create', ['--email' => 'existing@example.com'])
            ->expectsConfirmation('Пользователь с email «existing@example.com» уже существует. Назначить его администратором?', 'yes')
            ->assertExitCode(0);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_cancels_when_user_declines_promotion(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.com', 'is_admin' => false]);

        $this->artisan('admin:create', ['--email' => 'existing@example.com'])
            ->expectsConfirmation('Пользователь с email «existing@example.com» уже существует. Назначить его администратором?', 'no')
            ->expectsOutputToContain('Отменено')
            ->assertExitCode(1);

        $this->assertFalse($user->fresh()->is_admin);
    }
}
