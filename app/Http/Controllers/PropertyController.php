<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    // Display all properties
    public function index()
    {
        $properties = Property::with(['units', 'units.activeLease.tenant'])->latest()->paginate(10);

        return view('properties.index', compact('properties'));
    }

    // Show create form
    public function create()
    {
        return view('properties.create');
    }

    // Save new property
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_owner_id' => 'required|exists:property_owners,id',
            'name' => 'required|string|max:255',
            'property_type' => 'required|string|max:50',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        $property = Property::create($validated);

        // Create units if provided
        $units = $request->input('units', []);
        foreach ($units as $unitData) {
            if (empty($unitData['unit_number'])) {
                continue;
            }

            $unitRules = [
                'property_id' => 'required|exists:properties,id',
                'unit_number' => 'required|string|max:50',
                'unit_type' => 'required|string|max:50',
                'floor' => 'nullable|string|max:20',
                'monthly_rent' => 'required|numeric|min:0',
                'deposit_amount' => 'required|numeric|min:0',
                'status' => 'required|string|max:20',
                'description' => 'nullable|string',
            ];

            $unitValues = [
                'property_id' => $property->id,
                'unit_number' => $unitData['unit_number'],
                'unit_type' => $unitData['unit_type'] ?? 'studio',
                'floor' => $unitData['floor'] ?? null,
                'monthly_rent' => $unitData['monthly_rent'] ?? 0,
                'deposit_amount' => $unitData['deposit_amount'] ?? 0,
                'status' => $unitData['status'] ?? 'vacant',
                'description' => $unitData['description'] ?? null,
            ];

            Validator::make($unitValues, $unitRules)->validate();
            Unit::create($unitValues);
        }

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property and units created successfully.');
    }

    // Display a single property
    public function show(Property $property)
    {
        $property->load(['units.activeLease.tenant', 'units', 'expenses', 'maintenanceRequests']);

        return view('properties.show', compact('property'));
    }

    // Show edit form
    public function edit(Property $property)
    {
        return view('properties.edit', compact('property'));
    }

    // Update property
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'property_type' => 'required|string|max:50',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        $property->update($validated);

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }

    // Delete property
    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property deleted successfully.');
    }
}
