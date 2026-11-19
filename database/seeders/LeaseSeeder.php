<?php

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class LeaseSeeder extends Seeder
{
    public function run(): void
    {
        $amina = Tenant::where('email', 'amina@example.com')->firstOrFail();
        $john = Tenant::where('email', 'john@example.com')->firstOrFail();
        $unitA101 = Unit::where('unit_number', 'A101')->firstOrFail();
        $unitA102 = Unit::where('unit_number', 'A102')->firstOrFail();

        $activeLease = Lease::updateOrCreate(
            ['tenant_id' => $amina->id, 'unit_id' => $unitA101->id, 'start_date' => '2026-01-01'],
            [
                'end_date' => '2026-12-31',
                'monthly_rent' => 25000,
                'deposit_amount' => 25000,
                'payment_due_day' => 5,
                'status' => 'active',
                'notes' => 'Current active lease.',
            ],
        );
        $unitA101->update(['status' => 'occupied']);

        Lease::updateOrCreate(
            ['tenant_id' => $john->id, 'unit_id' => $unitA102->id, 'start_date' => '2025-01-01'],
            [
                'end_date' => '2025-12-31',
                'monthly_rent' => 38000,
                'deposit_amount' => 40000,
                'payment_due_day' => 1,
                'status' => 'expired',
                'notes' => 'Historical lease used to demonstrate lease history.',
            ],
        );
        $unitA102->update(['status' => 'vacant']);

        $activeLease->refresh();
    }
}
