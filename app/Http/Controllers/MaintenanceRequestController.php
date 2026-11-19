<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceRequestController extends Controller
{
    /**
     * Display maintenance requests based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = MaintenanceRequest::with(['tenant', 'unit.property', 'inspector']);

        // Tenants see only their own requests
        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();
            if ($tenant) {
                $query->where('tenant_id', $tenant->id);
            } else {
                $query->where('tenant_id', 0); // No results
            }
        }
        // Caretakers see requests for their assigned properties
        elseif ($user->hasAnyRole(['caretaker'])) {
            $profile = $user->profile;
            if ($profile && $profile->assigned_property) {
                $query->whereHas('unit', function ($q) use ($profile) {
                    $q->where('property_id', $profile->assigned_property);
                });
            } else {
                $query->where('property_id', 0); // No results
            }
        }
        // Owners see requests for their properties
        elseif ($user->hasAnyRole(['owner'])) {
            $query->whereHas('unit.property', function ($q) use ($user) {
                $q->where('property_owner_id', $user->id);
            });
        }
        // Admins and property managers see all
        elseif ($user->hasAnyRole(['admin', 'property_manager'])) {
            // All requests
        } else {
            abort(403, 'Unauthorized access.');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $maintenanceRequests = $query->latest()->paginate(15);

        // Get caretakers for assignment
        $caretakers = User::where('role', 'caretaker')->get();

        return view('maintenance.index', compact('maintenanceRequests', 'caretakers'));
    }

    /**
     * Show a specific maintenance request.
     */
    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $user = Auth::user();

        // Check authorization
        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();
            if (! $tenant || $maintenanceRequest->tenant_id !== $tenant->id) {
                abort(403, 'You can only view your own maintenance requests.');
            }
        } elseif ($user->hasAnyRole(['caretaker'])) {
            $profile = $user->profile;
            if (! $profile || ! $profile->assigned_property ||
                ! $maintenanceRequest->unit->property_id == $profile->assigned_property) {
                abort(403, 'You can only view maintenance requests for your assigned property.');
            }
        } elseif ($user->hasAnyRole(['owner'])) {
            if (! $maintenanceRequest->unit->property ||
                $maintenanceRequest->unit->property->property_owner_id !== $user->id) {
                abort(403, 'You can only view maintenance requests for your properties.');
            }
        } elseif (! $user->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Unauthorized access.');
        }

        $maintenanceRequest->load(['tenant', 'unit.property', 'inspector']);

        return view('maintenance.show', compact('maintenanceRequest'));
    }

    /**
     * Show the form for creating a new maintenance request (management).
     */
    public function create()
    {
        if (! Auth::user()->isManagement()) {
            abort(403, 'Only management can create maintenance requests.');
        }

        $properties = Property::with('units')->get();
        $tenants = Tenant::all();
        $units = Unit::with('property')->get();

        return view('maintenance.create', compact('properties', 'tenants', 'units'));
    }

    /**
     * Store a new maintenance request (management).
     */
    public function store(Request $request)
    {
        if (! Auth::user()->isManagement()) {
            abort(403, 'Only management can create maintenance requests.');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'unit_id' => 'required|exists:units,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $unit = Unit::find($validated['unit_id']);
        if ($unit->property_id != $validated['property_id']) {
            return back()->withErrors(['unit_id' => 'The selected unit does not belong to the selected property.']);
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'property_id' => $validated['property_id'],
            'unit_id' => $validated['unit_id'],
            'tenant_id' => $validated['tenant_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'reported_at' => now(),
        ]);

        // Notify tenant if specified
        if ($validated['tenant_id']) {
            $tenant = Tenant::find($validated['tenant_id']);
            $user = User::where('email', $tenant->email)->first();
            if ($user) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'tenant_id' => $tenant->id,
                    'title' => 'New Maintenance Request',
                    'message' => "A maintenance request has been created for your unit: {$validated['title']}",
                    'type' => 'maintenance',
                    'channel' => 'in_app',
                    'related_model' => 'maintenance_requests',
                    'related_id' => $maintenanceRequest->id,
                ]);
            }
        }

        return redirect()
            ->route('maintenance.index')
            ->with('success', 'Maintenance request created successfully.');
    }

    /**
     * Update maintenance request status and progress (caretaker/management).
     */
    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $user = Auth::user();

        // Caretakers can only update requests for their assigned properties
        if ($user->hasAnyRole(['caretaker'])) {
            $profile = $user->profile;
            if (! $profile || ! $profile->assigned_property ||
                $maintenanceRequest->unit->property_id != $profile->assigned_property) {
                abort(403, 'You can only update maintenance requests for your assigned property.');
            }
        }
        // Owners can only update requests for their properties
        elseif ($user->hasAnyRole(['owner'])) {
            if (! $maintenanceRequest->unit->property ||
                $maintenanceRequest->unit->property->property_owner_id !== $user->id) {
                abort(403, 'You can only update maintenance requests for your properties.');
            }
        }
        // Tenants cannot update via this route
        elseif ($user->hasAnyRole(['tenant'])) {
            abort(403, 'Tenants cannot update maintenance requests via this route.');
        } elseif (! $user->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:open,in_progress,awaiting_parts,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'resolved_at' => 'nullable|date',
        ]);

        // If status is resolved and no resolved_at, set it
        if ($validated['status'] === 'resolved' && ! $validated['resolved_at']) {
            $validated['resolved_at'] = now();
        }

        $maintenanceRequest->update($validated);

        // Notify tenant of status change
        if ($maintenanceRequest->tenant_id) {
            $tenant = Tenant::find($maintenanceRequest->tenant_id);
            $user = User::where('email', $tenant->email)->first();
            if ($user) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'tenant_id' => $tenant->id,
                    'title' => 'Maintenance Request Updated',
                    'message' => "Your maintenance request \"{$maintenanceRequest->title}\" status changed to: ".ucfirst($validated['status'] ?? $maintenanceRequest->status),
                    'type' => 'maintenance',
                    'channel' => 'in_app',
                    'related_model' => 'maintenance_requests',
                    'related_id' => $maintenanceRequest->id,
                ]);
            }
        }

        return redirect()
            ->route('maintenance.show', $maintenanceRequest)
            ->with('success', 'Maintenance request updated successfully.');
    }

    /**
     * Assign a caretaker to a maintenance request.
     */
    public function assign(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Only administrators can assign caretakers.');
        }

        $validated = $request->validate([
            'caretaker_id' => 'required|exists:users,id',
        ]);

        $caretaker = User::find($validated['caretaker_id']);
        if (! $caretaker->hasAnyRole(['caretaker'])) {
            return back()->withErrors(['caretaker_id' => 'Selected user is not a caretaker.']);
        }

        $maintenanceRequest->update([
            'inspector_id' => $validated['caretaker_id'],
        ]);

        // Notify caretaker
        AppNotification::create([
            'user_id' => $caretaker->id,
            'tenant_id' => $maintenanceRequest->tenant_id,
            'title' => 'Maintenance Request Assigned',
            'message' => "You have been assigned to a maintenance request: {$maintenanceRequest->title}",
            'type' => 'maintenance',
            'channel' => 'in_app',
            'related_model' => 'maintenance_requests',
            'related_id' => $maintenanceRequest->id,
        ]);

        return back()->with('success', 'Caretaker assigned successfully.');
    }

    /**
     * Delete a maintenance request.
     */
    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Only administrators can delete maintenance requests.');
        }

        $maintenanceRequest->delete();

        return redirect()
            ->route('maintenance.index')
            ->with('success', 'Maintenance request deleted successfully.');
    }
}
