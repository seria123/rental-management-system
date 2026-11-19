<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::updateOrCreate(
            ['email' => 'amina@example.com'],
            [
                'name' => 'Amina Mohamed',
                'phone' => '+254711111111',
                'national_id' => '12345678',
                'address' => 'Nairobi, Kenya',
                'emergency_contact_name' => 'Fatuma Mohamed',
                'emergency_contact_phone' => '+254722222222',
                'status' => 'active',
            ],
        );

        Tenant::updateOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Kamau',
                'phone' => '+254733333333',
                'national_id' => '87654321',
                'address' => 'Kiambu, Kenya',
                'emergency_contact_name' => 'Mary Kamau',
                'emergency_contact_phone' => '+254744444444',
                'status' => 'active',
            ],
        );
    }
}
