<?php

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $lease = Lease::where('status', 'active')->firstOrFail();
        $tenant = Tenant::where('email', 'amina@example.com')->firstOrFail();
        $invoice = RentInvoice::where('lease_id', $lease->id)
            ->where('billing_month', now()->startOfMonth())
            ->firstOrFail();

        Payment::updateOrCreate(
            ['transaction_reference' => 'QHF123456789'],
            [
                'tenant_id' => $tenant->id,
                'lease_id' => $lease->id,
                'rent_invoice_id' => $invoice->id,
                'amount' => 15000,
                'payment_date' => now(),
                'payment_method' => 'M-Pesa',
                'payment_type' => 'rent',
                'status' => 'completed',
                'notes' => 'Partial monthly rent payment.',
            ],
        );

        $invoice->update([
            'amount_paid' => 15000,
            'balance' => 10000,
            'status' => 'partial',
        ]);
    }
}
