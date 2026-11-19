<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyOwner;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_utility_routes_and_notifications_are_available(): void
    {
        $owner = PropertyOwner::create(['name' => 'Demo Owner', 'status' => 'active']);
        $property = Property::create([
            'property_owner_id' => $owner->id,
            'name' => 'Demo Property',
            'property_type' => 'apartment',
            'address' => 'Test street',
            'city' => 'Nairobi',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($user)->get(route('utilities.index'))->assertOk();
        $this->actingAs($user)->get(route('utilities.create'))->assertOk();
        $this->actingAs($user)->get(route('notifications.index'))->assertOk();
        $this->actingAs($user)->get(route('reports.index'))->assertOk();

        $this->assertDatabaseHas('properties', ['id' => $property->id]);
    }

    public function test_tenant_portal_routes_work_for_authenticated_tenant_users(): void
    {
        $tenant = Tenant::create([
            'name' => 'Portal Tenant',
            'email' => 'portal@example.com',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'email' => 'portal@example.com',
            'role' => User::ROLE_TENANT,
        ]);

        $this->actingAs($user)->get(route('portal.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('portal.statements'))->assertOk();
    }
}
