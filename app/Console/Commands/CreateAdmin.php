<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'admin:create')]
class CreateAdmin extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'admin:create
        {--email= : The administrator email address (default: admin@example.com)}
        {--name= : The administrator full name (default: System Administrator)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update the administrator account for Filament panel access.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->option('email') ?? 'admin@example.com';
        $name = $this->option('name') ?? 'System Administrator';

        $this->info('Administrator setup for Filament panel ('.$email.')');

        $password = $this->secret('Enter a secure password for the administrator:');
        $confirm = $this->secret('Confirm the administrator password:');

        if ($password !== $confirm) {
            $this->error('The passwords do not match. Aborting.');

            return self::INVALID;
        }

        if (strlen($password) < 8) {
            $this->error('The password must be at least 8 characters. Aborting.');

            return self::INVALID;
        }

        // updateOrCreate is keyed by email, so re-running this command resets
        // the password for the same administrator instead of creating a duplicate.
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => User::ROLE_ADMIN,
                'status' => 'active',
                'email_verified_at' => now(),
                'password' => Hash::make($password),
            ]
        );

        $this->info('Administrator account ready: '.$user->email);
        $this->info('Role: administrator ('.User::ROLE_ADMIN.')');
        $this->info('Filament panel URL: /admin');
        $this->warn('The password was never displayed and is not stored in source code.');
        $this->warn('To reset the administrator password, re-run: php artisan admin:create');

        return self::SUCCESS;
    }
}
