<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Ensure the default application users exist without managing passwords.
     * Existing accounts are updated by email; new accounts receive a random
     * unusable password until credentials are set through a secure workflow.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@example.com',
                'name' => 'System Administrator',
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'manager@example.com',
                'name' => 'Property Manager',
                'role' => User::ROLE_PROPERTY_MANAGER,
                'password' => env('SEED_PROPERTY_MANAGER_PASSWORD'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'accountant@example.com',
                'name' => 'Accountant',
                'role' => User::ROLE_ACCOUNTANT,
                'password' => env('SEED_ACCOUNTANT_PASSWORD'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'caretaker@example.com',
                'name' => 'Caretaker',
                'role' => User::ROLE_CARETAKER,
                'password' => env('SEED_CARETAKER_PASSWORD'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'stephenthuku@gmail.com',
                'name' => 'Developer',
                'role' => User::ROLE_DEVELOPER,
                'password' => env('SEED_DEVELOPER_PASSWORD'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'user@xample.com',
                'name' => 'Tenant user',
                'role' => User::ROLE_TENANT,
                'password' => env('SEED_TENANT_PASSWORD'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $email = $userData['email'];
            $existingUser = User::where('email', $email)->first();

            $defaults = [
                'name' => $userData['name'],
                'role' => $userData['role'],
                'status' => 'active',
                'email_verified_at' => $userData['email_verified_at'] ?? null,
            ];

            $seedPassword = $userData['password'] ?? null;

            if ($existingUser) {
                if (is_string($seedPassword) && $seedPassword !== '') {
                    $defaults['password'] = Hash::make($seedPassword);
                }

                $existingUser->update($defaults);

                continue;
            }

            User::create([
                ...$defaults,
                'email' => $email,
                'password' => is_string($seedPassword) && $seedPassword !== ''
                    ? Hash::make($seedPassword)
                    : Str::random(64),
            ]);
        }
    }
}
