<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserAuditAndRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_access_is_restricted_by_role_middleware(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make('password123'),
        ]);

        $manager = User::factory()->create([
            'email' => 'manager@example.com',
            'role' => User::ROLE_PROPERTY_MANAGER,
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($admin)->get('/admin/activity-log')->assertOk();
        $this->actingAs($manager)->get('/admin/activity-log')->assertStatus(403);
    }

    public function test_login_creates_activity_log_entry(): void
    {
        $user = User::factory()->create([
            'email' => 'login.user@example.com',
            'role' => User::ROLE_PROPERTY_MANAGER,
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('user_activity_logs', [
            'user_id' => $user->id,
            'event' => 'login',
        ]);
    }
}
