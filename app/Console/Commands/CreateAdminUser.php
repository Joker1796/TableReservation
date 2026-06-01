<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
                            {--name= : Имя пользователя}
                            {--email= : Email пользователя}
                            {--password= : Пароль}';

    protected $description = 'Создать первого администратора';

    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('Email');

        if (User::where('email', $email)->exists()) {
            $promote = $this->confirm("Пользователь с email «{$email}» уже существует. Назначить его администратором?");

            if ($promote) {
                User::where('email', $email)->update(['is_admin' => true]);
                $this->info("Пользователь «{$email}» теперь администратор.");

                return self::SUCCESS;
            }

            $this->warn('Отменено.');

            return self::FAILURE;
        }

        $name = $this->option('name') ?? $this->ask('Имя');
        $password = $this->option('password') ?? $this->secret('Пароль');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        $this->info("Администратор «{$name}» ({$email}) успешно создан.");

        return self::SUCCESS;
    }
}
