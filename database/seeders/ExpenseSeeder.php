<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $property = Property::where('name', 'Riverside Apartments')->firstOrFail();
        $unit = Unit::where('unit_number', 'A101')->firstOrFail();

        Expense::updateOrCreate(
            [
                'property_id' => $property->id,
                'unit_id' => $unit->id,
                'description' => 'Plumbing repair',
                'expense_date' => now()->toDateString(),
            ],
            [
                'category' => 'Repairs',
                'amount' => 4500,
                'payment_method' => 'M-Pesa',
                'reference' => 'EXP-001',
                'notes' => 'Kitchen sink repair.',
            ],
        );
    }
}
