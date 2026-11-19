<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\LeaseAgreement;
use App\Models\RentInvoice;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::with(['unit.property', 'leases', 'moveOutRequests'])
            ->latest()
            ->paginate(10);

        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = Unit::with('property')->get();

        return view('tenants.create', compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_number' => 'required|string|max:50|unique:tenants,id_number',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date|before:today',
            'occupation' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'month' => 'required|date_format:Y-m',
        ]);

        // Prevent assigning an occupied unit
        $occupied = Tenant::where('unit_id', $validated['unit_id'])
            ->exists();

        if ($occupied) {
            return back()
                ->withInput()
                ->withErrors(['unit_id' => 'This unit already has a registered tenant.']);
        }

        $tenant = Tenant::create($validated);

        $unit = Unit::find($validated['unit_id']);
        $startDate = $validated['month'].'-01';
        $endDate = date('Y-m-d', strtotime($startDate.' +1 year'));

        $lease = Lease::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $validated['unit_id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'monthly_rent' => $unit->monthly_rent,
            'deposit_amount' => $unit->deposit_amount,
            'status' => 'active',
        ]);

        // Create rent invoice for the billing month so outstanding balance is tracked
        $billingMonth = date('Y-m-01', strtotime($validated['month']));
        $dueDate = date('Y-m-t', strtotime($validated['month']));

        RentInvoice::create([
            'lease_id' => $lease->id,
            'tenant_id' => $tenant->id,
            'billing_month' => $billingMonth,
            'amount' => $unit->monthly_rent,
            'amount_paid' => 0,
            'balance' => $unit->monthly_rent,
            'due_date' => $dueDate,
            'status' => 'unpaid',
        ]);

        // Auto-create lease agreement from unit's stored agreement if it exists
        if ($unit->lease_agreement_path) {
            LeaseAgreement::create([
                'lease_id' => $lease->id,
                'file_path' => $unit->lease_agreement_path,
                'original_filename' => $unit->lease_agreement_filename,
                'owner_signed' => false,
                'tenant_signed' => false,
            ]);
        }

        return redirect()
            ->route('tenants.index')
            ->with('success', 'Tenant registered successfully.');
    }

    // Display a single tenant
    public function show(Tenant $tenant)
    {
        $tenant->load(['unit.property', 'leases', 'leases.unit']);

        return view('tenants.show', compact('tenant'));
    }

    // Show edit form
    public function edit(Tenant $tenant)
    {
        $units = Unit::with('property')->get();

        return view('tenants.edit', compact('tenant', 'units'));
    }

    // Update tenant information
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tenants', 'id_number')->ignore($tenant->id),
            ],
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date|before:today',
            'occupation' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'month' => 'required|date_format:Y-m',
        ]);

        $occupied = Tenant::where('unit_id', $validated['unit_id'])
            ->where('id', '!=', $tenant->id)
            ->exists();

        if ($occupied) {
            return back()
                ->withInput()
                ->withErrors(['unit_id' => 'This unit already has another tenant.']);
        }

        $tenant->update($validated);

        $unit = Unit::find($validated['unit_id']);
        $startDate = $validated['month'].'-01';
        $endDate = date('Y-m-d', strtotime($startDate.' +1 year'));

        $lease = Lease::updateOrCreate(
            ['tenant_id' => $tenant->id, 'status' => 'active'],
            [
                'unit_id' => $validated['unit_id'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'monthly_rent' => $unit->monthly_rent,
                'deposit_amount' => $unit->deposit_amount,
            ]
        );

        // Create/update rent invoice for the billing month
        $billingMonth = date('Y-m-01', strtotime($validated['month']));
        $dueDate = date('Y-m-t', strtotime($validated['month']));

        RentInvoice::updateOrCreate(
            ['lease_id' => $lease->id, 'billing_month' => $billingMonth],
            [
                'tenant_id' => $tenant->id,
                'amount' => $unit->monthly_rent,
                'amount_paid' => 0,
                'balance' => $unit->monthly_rent,
                'due_date' => $dueDate,
                'status' => 'unpaid',
            ]
        );

        // Auto-create lease agreement from unit's stored agreement if it exists and none exists yet
        if ($unit->lease_agreement_path && ! LeaseAgreement::where('lease_id', $lease->id)->exists()) {
            LeaseAgreement::create([
                'lease_id' => $lease->id,
                'file_path' => $unit->lease_agreement_path,
                'original_filename' => $unit->lease_agreement_filename,
                'owner_signed' => false,
                'tenant_signed' => false,
            ]);
        }

        return redirect()
            ->route('tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    // Delete tenant
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()
            ->route('tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }
}
