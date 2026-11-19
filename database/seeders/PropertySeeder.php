<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyOwner;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $owner = PropertyOwner::where('name', 'Ndiro Properties')->firstOrFail();

        Property::updateOrCreate(
            ['property_owner_id' => $owner->id, 'name' => 'Riverside Apartments'],
            [
                'property_type' => 'apartment',
                'address' => '10 River Road',
                'city' => 'Nairobi',
                'description' => 'A well-maintained residential apartment block.',
                'status' => 'active',
            ],
        );
    }
}
