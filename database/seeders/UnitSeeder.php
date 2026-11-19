<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $property = Property::where('name', 'Riverside Apartments')->firstOrFail();

        Unit::updateOrCreate(
            ['property_id' => $property->id, 'unit_number' => 'A101'],
            [
                'unit_type' => 'one_bedroom',
                'floor' => '1',
                'monthly_rent' => 25000,
                'deposit_amount' => 25000,
                'status' => 'vacant',
                'description' => 'Spacious one-bedroom unit with balcony.',
            ],
        );

        Unit::updateOrCreate(
            ['property_id' => $property->id, 'unit_number' => 'A102'],
            [
                'unit_type' => 'two_bedroom',
                'floor' => '1',
                'monthly_rent' => 40000,
                'deposit_amount' => 40000,
                'status' => 'vacant',
                'description' => 'Two-bedroom family unit.',
            ],
        );

        Unit::updateOrCreate(
            ['property_id' => $property->id, 'unit_number' => 'B201'],
            [
                'unit_type' => 'studio',
                'floor' => '2',
                'monthly_rent' => 18000,
                'deposit_amount' => 18000,
                'status' => 'vacant',
                'description' => 'Compact studio unit.',
            ],
        );
    }
}
