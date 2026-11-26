<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin {email?}';

    protected $description = 'Create or promote an administrator account';

    public function handle(): int
    {
        $email = $this->argument('email') ?? $this->ask('Correo del administrador');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Ingresa un correo electrónico válido.');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user && ! $this->confirm("¿Promover {$email} a administrador?", true)) {
            return self::FAILURE;
        }

        $name = $this->ask('Nombre', $user?->name ?? 'Administrador');
        $password = $this->secret('Contraseña (mínimo 12 caracteres)');
        $passwordConfirmation = $this->secret('Confirma la contraseña');

        if (strlen($password) < 12) {
            $this->error('La contraseña debe tener al menos 12 caracteres.');

            return self::FAILURE;
        }

        if (! hash_equals($password, $passwordConfirmation)) {
            $this->error('Las contraseñas no coinciden.');

            return self::FAILURE;
        }

        if ($user) {
            $user->forceFill([
                'name' => $name,
                'password' => $password,
                'email_verified_at' => now(),
                'is_admin' => true,
                'role' => 'administrator',
            ])->save();
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'is_admin' => true,
                'role' => 'administrator',
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $this->info("La cuenta {$email} ya puede acceder como administrador.");

        return self::SUCCESS;
    }
}
