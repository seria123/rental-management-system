<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_administrator_login_grants_access_to_the_filament_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $login = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $login->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
        $this->assertTrue($admin->refresh()->canAccessAdminPanel());
        $this->get('/admin')->assertOk();
        $this->get('/admin/login')->assertRedirect('/admin');
    }

    public function test_tenant_keeps_normal_login_access_but_cannot_access_filament(): void
    {
        $tenant = User::factory()->create([
            'role' => User::ROLE_TENANT,
            'status' => 'active',
        ]);

        $login = $this->post('/login', [
            'email' => $tenant->email,
            'password' => 'password',
        ]);

        $login->assertRedirect('/tenant/dashboard');
        $this->assertAuthenticatedAs($tenant);
        $this->get('/admin')->assertForbidden();
    }

    public static function nonAdministratorRoles(): array
    {
        return [
            'owner' => [User::ROLE_OWNER],
            'agent' => [User::ROLE_AGENT],
            'caretaker' => [User::ROLE_CARETAKER],
            'property manager' => [User::ROLE_PROPERTY_MANAGER],
            'accountant' => [User::ROLE_ACCOUNTANT],
            'developer' => [User::ROLE_DEVELOPER],
        ];
    }

    #[DataProvider('nonAdministratorRoles')]
    public function test_non_administrator_roles_cannot_access_filament(string $role): void
    {
        $user = User::factory()->create([
            'role' => $role,
            'status' => 'active',
        ]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}
