<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyOwner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateButtonsRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_primary_create_buttons_link_to_real_create_routes(): void
    {
        $user = User::factory()->create();

        PropertyOwner::create([
            'name' => 'Owner',
            'phone' => '0712345678',
            'email' => 'owner@example.com',
            'address' => 'Nairobi',
            'status' => 'active',
        ]);

        Property::create([
            'property_owner_id' => 1,
            'name' => 'Sample Property',
            'property_type' => 'Apartment',
            'address' => '123 Main St',
            'city' => 'Nairobi',
            'description' => 'Sample property',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $responses = [
            'properties' => $this->get(route('properties.index')),
            'tenants' => $this->get(route('tenants.index')),
            'payments' => $this->get(route('payments.index')),
            'units' => $this->get(route('units.index')),
        ];

        foreach ($responses as $label => $response) {
            $response->assertOk();
            $this->assertNotEmpty($response->getContent(), $label.' page should render Blade without fatal errors');
        }

        $this->assertStringContainsString('/properties/create', $this->get(route('properties.index'))->getContent());
        $this->assertStringContainsString('/tenants/create', $this->get(route('tenants.index'))->getContent());
        $this->assertStringContainsString('/payments/create', $this->get(route('payments.index'))->getContent());
        $this->assertStringContainsString('/units/create', $this->get(route('units.index'))->getContent());
    }
}
