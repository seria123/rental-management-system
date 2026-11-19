<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyOwner;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MpesaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_create_form_has_property_selector_inside_the_form_and_stores_expense(): void
    {
        $user = User::factory()->create();
        $owner = PropertyOwner::create([
            'name' => 'Test Owner',
            'phone' => '0712345678',
            'email' => 'owner@example.com',
            'address' => 'Test Address',
            'status' => 'active',
        ]);

        $property = Property::create([
            'property_owner_id' => $owner->id,
            'name' => 'Sunset Apartments',
            'property_type' => 'Apartment',
            'address' => '123 Main Street',
            'city' => 'Nairobi',
            'description' => 'Test property',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('expenses.create'));

        $response->assertOk();
        $response->assertSee('Record New Expense');
        $this->assertMatchesRegularExpression(
            '/<form[^>]*action="[^"]*\/expenses[^"]*"[^>]*>.*name="property_id"/s',
            $response->getContent(),
        );

        $storeResponse = $this->post(route('expenses.store'), [
            'property_id' => $property->id,
            'title' => 'Office supplies',
            'category' => 'Utilities',
            'amount' => '1500.00',
            'expense_date' => '2026-10-29',
            'description' => 'Power bill',
        ]);

        $storeResponse->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'property_id' => $property->id,
            'title' => 'Office supplies',
            'category' => 'Utilities',
            'amount' => '1500.00',
            'description' => 'Power bill',
        ]);
    }

    public function test_mpesa_phone_number_is_saved_for_expense_and_payment_records(): void
    {
        $user = User::factory()->create();
        $owner = PropertyOwner::create([
            'name' => 'Test Owner',
            'phone' => '0712345678',
            'email' => 'owner@example.com',
            'address' => 'Test Address',
            'status' => 'active',
        ]);

        $property = Property::create([
            'property_owner_id' => $owner->id,
            'name' => 'Sunset Apartments',
            'property_type' => 'Apartment',
            'address' => '123 Main Street',
            'city' => 'Nairobi',
            'description' => 'Test property',
            'status' => 'active',
        ]);

        $tenant = Tenant::create([
            'name' => 'Jane Tenant',
            'phone' => '0712345678',
            'email' => 'jane@example.com',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('expenses.create'));
        $response->assertOk();
        $response->assertSee('M-Pesa Phone Number');

        $this->post(route('expenses.store'), [
            'property_id' => $property->id,
            'title' => 'Maintenance bill',
            'category' => 'Maintenance',
            'amount' => '3500.00',
            'expense_date' => '2026-11-01',
            'description' => 'Water tank repair',
            'payment_method' => 'Mpesa',
            'phone_number' => '0712345678',
        ])->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'title' => 'Maintenance bill',
            'payment_method' => 'Mpesa',
            'phone_number' => '0712345678',
        ]);

        $this->post(route('payments.store'), [
            'tenant_id' => $tenant->id,
            'lease_id' => null,
            'amount' => '2500.00',
            'payment_date' => '2026-11-03',
            'payment_method' => 'Mpesa',
            'transaction_reference' => 'REF-123',
            'payment_type' => 'rent',
            'status' => 'completed',
            'phone_number' => '0712345678',
        ])->assertRedirect(route('payments.index'));

        $this->assertDatabaseHas('payments', [
            'tenant_id' => $tenant->id,
            'payment_method' => 'Mpesa',
            'phone_number' => '0712345678',
        ]);
    }

    public function test_payment_uses_system_setting_credentials_for_stk_push(): void
    {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Jane Tenant',
            'phone' => '0712345678',
            'email' => 'jane@example.com',
            'status' => 'active',
        ]);

        foreach ([
            ['key' => 'mpesa_consumer_key', 'value' => 'consumer-key', 'is_encrypted' => true],
            ['key' => 'mpesa_consumer_secret', 'value' => 'consumer-secret', 'is_encrypted' => true],
            ['key' => 'mpesa_passkey', 'value' => 'passkey', 'is_encrypted' => true],
            ['key' => 'mpesa_paybill', 'value' => '174379', 'is_encrypted' => false],
            ['key' => 'mpesa_callback_url', 'value' => 'https://example.com/mpesa/callback', 'is_encrypted' => false],
        ] as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => 'payment',
                    'value' => $setting['is_encrypted'] ? encrypt($setting['value']) : $setting['value'],
                    'is_encrypted' => $setting['is_encrypted'],
                ]
            );
        }

        $this->actingAs($user);

        $this->instance(MpesaService::class, new class extends MpesaService
        {
            public function stkPush(string $phone, float $amount, string $accountReference, string $description = 'Payment'): array
            {
                return [
                    'MerchantRequestID' => 'MERCHANT-123',
                    'CheckoutRequestID' => 'CHECKOUT-123',
                    'ResponseCode' => '0',
                    'ResponseDescription' => 'Success',
                ];
            }
        });

        $response = $this->post(route('payments.store'), [
            'tenant_id' => $tenant->id,
            'lease_id' => null,
            'amount' => '2500.00',
            'payment_date' => '2026-11-03',
            'payment_method' => 'Mpesa',
            'transaction_reference' => 'REF-123',
            'payment_type' => 'rent',
            'status' => 'completed',
            'phone_number' => '0712345678',
        ]);

        $response->assertRedirect(route('payments.index'));
        $this->assertDatabaseHas('payments', [
            'tenant_id' => $tenant->id,
            'payment_method' => 'Mpesa',
            'phone_number' => '0712345678',
            'status' => 'pending',
        ]);
    }
}
