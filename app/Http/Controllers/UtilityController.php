<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Tenant;
use App\Models\UtilityBill;
use App\Models\UtilityReading;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    public function index()
    {
        $bills = UtilityBill::with(['property', 'unit', 'tenant'])->latest('billing_period')->paginate(10);

        return view('utilities.index', compact('bills'));
    }

    public function create()
    {
        $properties = Property::with('units')->get();
        $tenants = Tenant::with('unit')->get();

        return view('utilities.create', compact('properties', 'tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'tenant_id' => ['nullable', 'exists:tenants,id'],
            'bill_type' => ['required', 'in:water,electricity,garbage,security,gas,other'],
            'billing_period' => ['required', 'date'],
            'reading_value' => ['nullable', 'numeric', 'min:0'],
            'consumed_units' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,paid,overdue'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->filled('reading_value') || $request->filled('consumed_units')) {
            UtilityReading::create([
                'property_id' => $validated['property_id'],
                'unit_id' => $validated['unit_id'],
                'tenant_id' => $validated['tenant_id'],
                'reading_type' => $validated['bill_type'],
                'reading_value' => $validated['reading_value'] ?? 0,
                'consumed_units' => $validated['consumed_units'] ?? 0,
                'billing_period' => $validated['billing_period'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
            ]);
        }

        UtilityBill::create([
            'property_id' => $validated['property_id'],
            'unit_id' => $validated['unit_id'],
            'tenant_id' => $validated['tenant_id'],
            'bill_type' => $validated['bill_type'],
            'billing_period' => $validated['billing_period'],
            'amount' => $validated['amount'],
            'due_date' => $validated['due_date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('utilities.index')->with('success', 'Utility bill recorded successfully.');
    }
}
