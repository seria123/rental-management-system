<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SystemSettingSeeder::class,
            PropertyOwnerSeeder::class,
            PropertySeeder::class,
            UnitSeeder::class,
            TenantSeeder::class,
            LeaseSeeder::class,
            RentInvoiceSeeder::class,
            PaymentSeeder::class,
            ExpenseSeeder::class,
            MaintenanceRequestSeeder::class,
        ]);
    }
}
