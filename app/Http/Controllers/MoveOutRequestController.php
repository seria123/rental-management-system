<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\MoveOutRequest;
use App\Models\RentInvoice;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MoveOutRequestController extends Controller
{
    /**
     * Show the move-out request form for a tenant.
     */
    public function create(Tenant $tenant)
    {
        $activeLease = $tenant->leases()->where('status', 'active')->first();

        if (! $activeLease) {
            return back()->withErrors(['error' => 'No active lease found for this tenant.']);
        }

        return view('move_out_requests.create', compact('tenant'));
    }

    /**
     * Tenant submits a move-out request.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'requested_move_out_date' => 'required|date|after:today',
            'reason' => 'nullable|string|max:1000',
        ]);

        $lease = $tenant->leases()->where('status', 'active')->first();

        if (! $lease) {
            return back()->withErrors(['error' => 'No active lease found for this tenant.']);
        }

        $unit = $lease->unit;

        // Check if there's already a pending request
        $existing = MoveOutRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'A move-out request is already pending for this tenant.']);
        }

        $noticeDays = 5; // 5 days notice requirement

        $moveOutRequest = MoveOutRequest::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'lease_id' => $lease->id,
            'requested_move_out_date' => $validated['requested_move_out_date'],
            'notice_days' => $noticeDays,
            'reason' => $validated['reason'],
            'status' => 'pending',
            'deposit_amount' => $lease->deposit_amount,
            'deposit_return_amount' => 0,
            'deposit_deduction_amount' => 0,
            'damage_cost' => 0,
        ]);

        // Notify owner/agent about the move-out request
        $owners = User::whereIn('role', ['admin', 'property_manager'])->get();
        foreach ($owners as $owner) {
            AppNotification::create([
                'user_id' => $owner->id,
                'tenant_id' => $tenant->id,
                'title' => 'Move-Out Request Submitted',
                'message' => "Tenant {$tenant->name} has requested to move out on {$validated['requested_move_out_date']} (unit {$unit->unit_number}). Please review.",
                'type' => 'move_out',
                'channel' => 'in_app',
                'related_model' => 'move_out_requests',
                'related_id' => $moveOutRequest->id,
            ]);
        }

        return redirect()
            ->route('tenants.show', $tenant->id)
            ->with('success', 'Move-out request submitted successfully. The owner will review it.');
    }

    /**
     * Schedule a property inspection (assign caretaker).
     */
    public function scheduleInspection(Request $request, MoveOutRequest $moveOutRequest)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Only property managers can schedule inspections.');
        }

        $validated = $request->validate([
            'inspector_id' => ['required', 'exists:users,id'],
            'inspection_scheduled_at' => ['required', 'date', 'after:now'],
            'inspection_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $inspector = User::find($validated['inspector_id']);
        if (! $inspector->hasAnyRole(['caretaker'])) {
            return back()->withErrors(['error' => 'Only caretakers can be assigned as inspectors.']);
        }

        $moveOutRequest->update([
            'status' => 'inspection_scheduled',
            'inspector_id' => $validated['inspector_id'],
            'inspection_scheduled_at' => $validated['inspection_scheduled_at'],
            'inspection_status' => 'scheduled',
            'inspection_notes' => $validated['inspection_notes'],
        ]);

        // Notify caretaker
        AppNotification::create([
            'user_id' => $inspector->id,
            'tenant_id' => $moveOutRequest->tenant_id,
            'title' => 'Inspection Scheduled',
            'message' => "A move-out inspection has been scheduled for unit {$moveOutRequest->unit->unit_number} on {$validated['inspection_scheduled_at']}.",
            'type' => 'maintenance',
            'channel' => 'in_app',
            'related_model' => 'move_out_requests',
            'related_id' => $moveOutRequest->id,
        ]);

        // Notify tenant
        AppNotification::create([
            'tenant_id' => $moveOutRequest->tenant_id,
            'title' => 'Move-Out Inspection Scheduled',
            'message' => "An inspection of your unit has been scheduled for {$validated['inspection_scheduled_at']}.",
            'type' => 'move_out',
            'channel' => 'in_app',
            'related_model' => 'move_out_requests',
            'related_id' => $moveOutRequest->id,
        ]);

        return redirect()->route('move-out-requests.index')->with('success', 'Inspection scheduled successfully.');
    }

    /**
     * Complete a property inspection (caretaker records findings).
     */
    public function completeInspection(Request $request, MoveOutRequest $moveOutRequest)
    {
        if (! Auth::user()->hasAnyRole(['caretaker'])) {
            abort(403, 'Only caretakers can complete inspections.');
        }

        if ($moveOutRequest->inspector_id !== Auth::id()) {
            abort(403, 'You are not assigned to this inspection.');
        }

        $validated = $request->validate([
            'inspection_notes' => ['required', 'string', 'max:2000'],
            'damage_notes' => ['nullable', 'string', 'max:2000'],
            'damage_cost' => ['nullable', 'numeric', 'min:0'],
            'inspection_status' => ['required', 'in:completed'],
        ]);

        $moveOutRequest->update([
            'inspection_status' => 'completed',
            'inspection_completed_at' => now(),
            'inspection_notes' => $validated['inspection_notes'],
            'damage_notes' => $validated['damage_notes'],
            'damage_cost' => $validated['damage_cost'] ?? 0,
            'status' => 'under_review',
        ]);

        // Notify property managers
        $managers = User::whereIn('role', ['admin', 'property_manager'])->get();
        foreach ($managers as $manager) {
            AppNotification::create([
                'user_id' => $manager->id,
                'tenant_id' => $moveOutRequest->tenant_id,
                'title' => 'Inspection Completed',
                'message' => "Caretaker has completed inspection for unit {$moveOutRequest->unit->unit_number}. Review the findings.",
                'type' => 'maintenance',
                'channel' => 'in_app',
                'related_model' => 'move_out_requests',
                'related_id' => $moveOutRequest->id,
            ]);
        }

        return redirect()->route('move-out-requests.index')->with('success', 'Inspection completed successfully.');
    }

    /**
     * Owner views all move-out requests.
     */
    public function index()
    {
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Only property owners/agents can view move-out requests.');
        }

        $requests = MoveOutRequest::with(['tenant', 'unit.property', 'lease'])
            ->latest()
            ->paginate(10);

        return view('move_out_requests.index', compact('requests'));
    }

    /**
     * Owner approves a move-out request.
     */
    public function approve(Request $request, MoveOutRequest $moveOutRequest)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Only property owners/agents can approve move-out requests.');
        }

        $request->validate([
            'owner_response_notes' => 'nullable|string|max:1000',
        ]);

        $moveOutRequest->update([
            'status' => 'approved',
            'owner_approved_at' => now(),
            'owner_response_notes' => $request->owner_response_notes,
        ]);

        // Notify tenant
        AppNotification::create([
            'tenant_id' => $moveOutRequest->tenant_id,
            'title' => 'Move-Out Request Approved',
            'message' => "Your move-out request for {$moveOutRequest->unit->unit_number} has been approved. Move-out date: {$moveOutRequest->requested_move_out_date}.",
            'type' => 'move_out',
            'channel' => 'in_app',
            'related_model' => 'move_out_requests',
            'related_id' => $moveOutRequest->id,
        ]);

        return redirect()
            ->route('move-out-requests.index')
            ->with('success', 'Move-out request approved.');
    }

    /**
     * Owner rejects a move-out request.
     */
    public function reject(Request $request, MoveOutRequest $moveOutRequest)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Only property owners/agents can reject move-out requests.');
        }

        $request->validate([
            'owner_response_notes' => 'required|string|max:1000',
        ]);

        $moveOutRequest->update([
            'status' => 'rejected',
            'owner_response_notes' => $request->owner_response_notes,
        ]);

        AppNotification::create([
            'tenant_id' => $moveOutRequest->tenant_id,
            'title' => 'Move-Out Request Rejected',
            'message' => "Your move-out request for {$moveOutRequest->unit->unit_number} was rejected. Reason: {$request->owner_response_notes}",
            'type' => 'move_out',
            'channel' => 'in_app',
            'related_model' => 'move_out_requests',
            'related_id' => $moveOutRequest->id,
        ]);

        return redirect()
            ->route('move-out-requests.index')
            ->with('success', 'Move-out request rejected.');
    }

    /**
     * Show the deposit processing form for an approved move-out request.
     */
    public function processEdit(MoveOutRequest $moveOutRequest)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Only property owners/agents can process move-outs.');
        }

        if (! $moveOutRequest->isApproved()) {
            return back()->withErrors(['error' => 'Only approved move-out requests can be processed.']);
        }

        return view('move_out_requests.process', compact('moveOutRequest'));
    }

    /**
     * Owner processes deposit return/deduction for a completed move-out.
     */
    public function process(Request $request, MoveOutRequest $moveOutRequest)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Only property owners/agents can process move-outs.');
        }

        $request->validate([
            'deposit_return_amount' => 'required|numeric|min:0',
            'deposit_deduction_amount' => 'required|numeric|min:0',
            'damage_notes' => 'nullable|string|max:1000',
            'damage_cost' => 'required|numeric|min:0',
        ]);

        if ($moveOutRequest->status === 'completed') {
            return back()->withErrors(['error' => 'This move-out request has already been processed.']);
        }

        if (! $moveOutRequest->isApproved()) {
            return back()->withErrors(['error' => 'Only approved move-out requests can be processed.']);
        }

        $moveOutRequest->loadMissing(['tenant', 'unit', 'lease']);

        if (! $moveOutRequest->tenant) {
            return back()->withErrors(['error' => 'Tenant record not found for this move-out request.']);
        }

        if (! $moveOutRequest->unit) {
            return back()->withErrors(['error' => 'Unit record not found for this move-out request.']);
        }

        if (! $moveOutRequest->lease) {
            return back()->withErrors(['error' => 'Lease record not found for this move-out request.']);
        }

        // Validate that return + deduction = deposit
        $total = (float) $request->deposit_return_amount + (float) $request->deposit_deduction_amount;
        $depositAmount = (float) $moveOutRequest->deposit_amount;
        if (abs($total - $depositAmount) > 0.01) {
            return back()->withErrors([
                'error' => "Return amount ({$request->deposit_return_amount}) + Deduction amount ({$request->deposit_deduction_amount}) must equal the deposit amount (".number_format($depositAmount, 2).').',
            ])->withInput();
        }

        try {
            DB::transaction(function () use ($request, $moveOutRequest) {
                $moveOutRequest->update([
                    'status' => 'completed',
                    'actual_move_out_date' => now()->toDateString(),
                    'deposit_return_amount' => $request->deposit_return_amount,
                    'deposit_deduction_amount' => $request->deposit_deduction_amount,
                    'damage_notes' => $request->damage_notes,
                    'damage_cost' => $request->damage_cost,
                    'processed_at' => now(),
                ]);

                $unit = $moveOutRequest->unit;
                if ($unit) {
                    $unit->update(['status' => 'vacant']);
                }

                $lease = $moveOutRequest->lease;
                if ($lease) {
                    $lease->update(['status' => 'ended']);
                }

                $tenant = $moveOutRequest->tenant;
                if ($tenant) {
                    $tenant->update(['status' => 'inactive']);
                }
            });
        } catch (\Exception $e) {
            Log::error('Move-out processing failed', [
                'move_out_request_id' => $moveOutRequest->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to process move-out. Please try again.'])->withInput();
        }

        $outstandingRent = 0;
        $outstandingUtilities = 0;
        try {
            if ($moveOutRequest->lease_id) {
                $outstandingRent = RentInvoice::where('lease_id', $moveOutRequest->lease_id)
                    ->where('status', '!=', 'paid')
                    ->sum('balance');
            }
            if (class_exists(UtilityBill::class)) {
                $outstandingUtilities = UtilityBill::where('lease_id', $moveOutRequest->lease_id)
                    ->where('status', '!=', 'paid')
                    ->sum('balance');
            }
        } catch (\Exception $e) {
            // ignore
        }

        AppNotification::create([
            'tenant_id' => $moveOutRequest->tenant_id,
            'title' => 'Move-Out Processed',
            'message' => 'Your move-out has been processed. Deposit return: KSh '.number_format((float) $request->deposit_return_amount, 2).', Deduction: KSh '.number_format((float) $request->deposit_deduction_amount, 2).($outstandingRent > 0 ? ', Outstanding rent: KSh '.number_format((float) $outstandingRent, 2) : '').($outstandingUtilities > 0 ? ', Outstanding utilities: KSh '.number_format((float) $outstandingUtilities, 2) : ''),
            'type' => 'move_out',
            'channel' => 'in_app',
            'related_model' => 'move_out_requests',
            'related_id' => $moveOutRequest->id,
        ]);

        return redirect()
            ->route('move-out-requests.index')
            ->with('success', 'Move-out processed. Deposit return and deduction recorded.');
    }

    /**
     * Show 5-day advance notifications for upcoming move-outs.
     */
    public function upcoming()
    {
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            abort(403, 'Only property owners/agents can view upcoming move-outs.');
        }

        $upcoming = MoveOutRequest::with(['tenant', 'unit.property'])
            ->where('status', 'approved')
            ->whereBetween('requested_move_out_date', [now(), now()->addDays(5)])
            ->get();

        // Auto-create 5-day advance notifications for owners
        foreach ($upcoming as $request) {
            $alreadyNotified = AppNotification::where('tenant_id', $request->tenant_id)
                ->where('title', '5-Day Move-Out Advance Notice')
                ->where('related_id', $request->id)
                ->exists();

            if (! $alreadyNotified) {
                $owners = User::whereIn('role', ['admin', 'property_manager'])->get();
                foreach ($owners as $owner) {
                    AppNotification::create([
                        'user_id' => $owner->id,
                        'tenant_id' => $request->tenant_id,
                        'title' => '5-Day Move-Out Advance Notice',
                        'message' => "Tenant {$request->tenant->name} is moving out of unit {$request->unit->unit_number} on {$request->requested_move_out_date->format('d M Y')} (in {$request->requested_move_out_date->diffInDays(now())} days). Please prepare for unit inspection and deposit processing.",
                        'type' => 'move_out_advance',
                        'channel' => 'in_app',
                        'related_model' => 'move_out_requests',
                        'related_id' => $request->id,
                    ]);
                }
            }
        }

        return view('move_out_requests.upcoming', compact('upcoming'));
    }
}
