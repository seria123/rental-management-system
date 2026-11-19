<?php

namespace Database\Seeders;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class MaintenanceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $property = Property::where('name', 'Riverside Apartments')->firstOrFail();
        $unit = Unit::where('unit_number', 'A101')->firstOrFail();
        $tenant = Tenant::where('email', 'amina@example.com')->firstOrFail();

        MaintenanceRequest::updateOrCreate(
            [
                'property_id' => $property->id,
                'unit_id' => $unit->id,
                'title' => 'Water leakage',
                'reported_at' => now()->subDays(2)->startOfDay(),
            ],
            [
                'tenant_id' => $tenant->id,
                'description' => 'Water is leaking from the kitchen pipe.',
                'priority' => 'high',
                'status' => 'open',
                'resolved_at' => null,
            ],
        );
    }
}
