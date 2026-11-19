<?php

namespace Database\Seeders;

use App\Models\PropertyOwner;
use Illuminate\Database\Seeder;

class PropertyOwnerSeeder extends Seeder
{
    public function run(): void
    {
        PropertyOwner::updateOrCreate(
            ['name' => 'Ndiro Properties'],
            [
                'phone' => '+254700000000',
                'email' => 'owner@ndiro.example',
                'address' => 'Nairobi, Kenya',
                'status' => 'active',
            ],
        );
    }
}
