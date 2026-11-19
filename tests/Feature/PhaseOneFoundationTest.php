<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\PropertyOwner;
use App\Models\RentInvoice;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_phase_one_seed_data_and_relationships_are_available(): void
    {
        $this->seed();

        $admin = User::where('role', User::ROLE_ADMIN)->firstOrFail();
        $this->assertSame('System Administrator', $admin->name);

        $property = Property::where('name', 'Riverside Apartments')->firstOrFail();
        $this->assertCount(3, $property->units);
        $this->assertCount(2, $property->leases);

        $unit = Unit::where('unit_number', 'A101')->firstOrFail();
        $this->assertSame('occupied', $unit->status);
        $this->assertTrue($unit->activeLease->exists());

        $lease = $unit->activeLease;
        $this->assertInstanceOf(Lease::class, $lease);
        $this->assertSame('amina@example.com', $lease->tenant->email);
        $this->assertCount(1, $lease->invoices);
        $this->assertCount(1, $lease->payments);

        $invoice = $lease->invoices->firstOrFail();
        $this->assertInstanceOf(RentInvoice::class, $invoice);
        $this->assertEquals(10000, $invoice->balance);
        $this->assertSame('partial', $invoice->status);

        $expense = Expense::firstOrFail();
        $this->assertSame($property->id, $expense->property_id);
        $this->assertSame($unit->id, $expense->unit_id);

        $request = MaintenanceRequest::firstOrFail();
        $this->assertSame($property->id, $request->property_id);
        $this->assertSame($unit->id, $request->unit_id);
        $this->assertSame('open', $request->status);
    }

    public function test_phase_one_records_can_be_created_updated_and_deleted(): void
    {
        $owner = PropertyOwner::create([
            'name' => 'CRUD Owner',
            'status' => 'active',
        ]);
        $property = $owner->properties()->create([
            'name' => 'CRUD Property',
            'property_type' => 'apartment',
            'address' => '1 Test Street',
            'city' => 'Nairobi',
            'status' => 'active',
        ]);
        $unit = $property->units()->create([
            'unit_number' => 'C101',
            'unit_type' => 'studio',
            'monthly_rent' => 15000,
            'deposit_amount' => 15000,
            'status' => 'vacant',
        ]);
        $tenant = Tenant::create([
            'name' => 'CRUD Tenant',
            'email' => 'crud@example.com',
            'status' => 'active',
        ]);
        $lease = $unit->leases()->create([
            'tenant_id' => $tenant->id,
            'start_date' => '2026-10-01',
            'end_date' => '2027-09-30',
            'monthly_rent' => 15000,
            'deposit_amount' => 15000,
            'payment_due_day' => 1,
            'status' => 'pending',
        ]);
        $invoice = $lease->invoices()->create([
            'tenant_id' => $tenant->id,
            'billing_month' => '2026-10-01',
            'amount' => 15000,
            'amount_paid' => 0,
            'balance' => 15000,
            'due_date' => '2026-10-05',
            'status' => 'unpaid',
        ]);
        $payment = $invoice->payments()->create([
            'tenant_id' => $tenant->id,
            'lease_id' => $lease->id,
            'amount' => 15000,
            'payment_date' => '2026-10-05',
            'payment_method' => 'Cash',
            'payment_type' => 'rent',
            'status' => 'completed',
        ]);
        $expense = $property->expenses()->create([
            'unit_id' => $unit->id,
            'description' => 'Test expense',
            'category' => 'Other',
            'amount' => 1000,
            'expense_date' => '2026-10-06',
        ]);
        $request = $unit->maintenanceRequests()->create([
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'title' => 'Test request',
            'description' => 'Test maintenance request',
            'priority' => 'low',
            'status' => 'open',
            'reported_at' => '2026-10-06 10:00:00',
        ]);

        $unit->update(['status' => 'maintenance']);
        $invoice->update(['status' => 'paid', 'balance' => 0]);

        $this->assertSame('maintenance', $unit->fresh()->status);
        $this->assertSame('paid', $invoice->fresh()->status);
        $this->assertDatabaseHas('expenses', ['id' => $expense->id]);
        $this->assertDatabaseHas('maintenance_requests', ['id' => $request->id]);

        $payment->delete();
        $invoice->delete();
        $lease->delete();
        $unit->delete();
        $tenant->delete();
        $property->delete();
        $owner->delete();

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
        $this->assertDatabaseMissing('property_owners', ['id' => $owner->id]);
    }
}
