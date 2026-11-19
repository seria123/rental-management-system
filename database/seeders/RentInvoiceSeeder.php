<?php

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\RentInvoice;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class RentInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $lease = Lease::where('status', 'active')->firstOrFail();
        $tenant = Tenant::where('email', 'amina@example.com')->firstOrFail();

        RentInvoice::updateOrCreate(
            ['lease_id' => $lease->id, 'billing_month' => now()->startOfMonth()],
            [
                'tenant_id' => $tenant->id,
                'amount' => $lease->monthly_rent,
                'amount_paid' => 15000,
                'balance' => 10000,
                'due_date' => now()->addDays(5),
                'status' => 'partial',
                'description' => 'Monthly rent invoice.',
            ],
        );
    }
}
