<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\AppNotification;
use App\Models\Lease;
use App\Models\LeaseAgreement;
use App\Models\MaintenanceRequest;
use App\Models\MoveOutRequest;
use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PortalController extends Controller
{
    public function dashboard()
    {
        if (! Auth::user()->hasAnyRole(['tenant'])) {
            return redirect('/dashboard');
        }

        $tenant = Tenant::where('email', Auth::user()->email)->first();

        if (! $tenant) {
            abort(403, 'Tenant record not found.');
        }

        $tenant->load(['unit.property', 'currentLease']);

        $lease = $tenant->currentLease;
        $payments = Payment::where('tenant_id', $tenant->id)->latest('payment_date')->take(5)->get();
        $maintenance = MaintenanceRequest::where('tenant_id', $tenant->id)->latest('reported_at')->take(5)->get();
        $moveOutRequests = MoveOutRequest::where('tenant_id', $tenant->id)->latest()->take(3)->get();

        // Get announcements for this tenant
        $announcements = Announcement::forTenant($tenant)
            ->orderBy('is_pinned', 'desc')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Mark announcements as read
        foreach ($announcements as $announcement) {
            AnnouncementRecipient::updateOrCreate(
                ['announcement_id' => $announcement->id, 'tenant_id' => $tenant->id],
                ['is_read' => true, 'read_at' => now()]
            );
        }

        // Calculate outstanding balance
        $outstandingBalance = 0;
        $totalPaid = 0;
        $pendingTransactions = 0;
        $successfulPayments = 0;
        $failedTransactions = 0;
        $nextDueDate = null;
        $receiptNumbers = [];

        if ($lease) {
            $outstandingBalance = (float) RentInvoice::where('lease_id', $lease->id)
                ->where('balance', '>', 0)
                ->sum('balance');
            $nextDueDate = RentInvoice::where('lease_id', $lease->id)
                ->where('balance', '>', 0)
                ->orderBy('due_date', 'asc')
                ->value('due_date');

            $completedPayments = Payment::where('tenant_id', $tenant->id)
                ->where('status', 'completed')
                ->get();

            $totalPaid = (float) $completedPayments->sum('amount');
            $receiptNumbers = $completedPayments
                ->whereNotNull('transaction_id')
                ->pluck('transaction_id')
                ->filter()
                ->values()
                ->all();

            $pendingTransactions = (int) Payment::where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->count();

            $successfulPayments = (int) Payment::where('tenant_id', $tenant->id)
                ->where('status', 'completed')
                ->count();

            $failedTransactions = (int) Payment::where('tenant_id', $tenant->id)
                ->where('status', 'failed')
                ->count();
        }

        return view('portal.dashboard', compact(
            'tenant',
            'lease',
            'payments',
            'maintenance',
            'moveOutRequests',
            'announcements',
            'outstandingBalance',
            'totalPaid',
            'pendingTransactions',
            'successfulPayments',
            'failedTransactions',
            'receiptNumbers',
            'nextDueDate'
        ));
    }

    public function statements()
    {
        $tenant = Tenant::where('email', Auth::user()->email)->first();

        if (! $tenant) {
            abort(403, 'Tenant record not found.');
        }

        $payments = Payment::where('tenant_id', $tenant->id)->latest('payment_date')->get();
        $invoices = RentInvoice::where('tenant_id', $tenant->id)->latest('billing_month')->get();

        return view('portal.statements', compact('tenant', 'payments', 'invoices'));
    }

    /**
     * View the tenant's lease agreement.
     */
    public function lease()
    {
        $tenant = Tenant::where('email', Auth::user()->email)->first();

        if (! $tenant) {
            abort(403, 'Tenant record not found.');
        }

        $tenant->load(['unit.property', 'currentLease']);
        $lease = $tenant->currentLease;

        if (! $lease) {
            abort(404, 'No active lease found.');
        }

        $agreement = LeaseAgreement::where('lease_id', $lease->id)->first();

        return view('portal.lease', compact('tenant', 'lease', 'agreement'));
    }

    /**
     * Download the lease agreement document.
     */
    public function downloadLease()
    {
        $tenant = Tenant::where('email', Auth::user()->email)->first();

        if (! $tenant) {
            abort(403, 'Tenant record not found.');
        }

        $tenant->load(['currentLease']);
        $lease = $tenant->currentLease;

        if (! $lease) {
            abort(404, 'No active lease found.');
        }

        $agreement = LeaseAgreement::where('lease_id', $lease->id)->first();

        if (! $agreement || ! Storage::exists($agreement->file_path)) {
            abort(404, 'Lease agreement document not found.');
        }

        return Storage::download($agreement->file_path, $agreement->original_filename);
    }

    public function maintenanceCreate()
    {
        $tenant = Tenant::where('email', Auth::user()->email)->first();

        if (! $tenant) {
            abort(403, 'Tenant record not found.');
        }

        return view('portal.maintenance-create', compact('tenant'));
    }

    public function maintenanceStore(Request $request)
    {
        $tenant = Tenant::where('email', Auth::user()->email)->first();

        if (! $tenant) {
            abort(403, 'Tenant record not found.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $file->store(
                    'maintenance-attachments/'.$tenant->id,
                    'local'
                );
            }
        }

        MaintenanceRequest::create([
            'property_id' => $tenant->unit?->property_id ?? 1,
            'unit_id' => $tenant->unit_id ?? 1,
            'tenant_id' => $tenant->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'reported_at' => now(),
        ]);

        // Notify property managers/admins
        $managers = User::whereIn('role', ['admin', 'property_manager'])->get();
        foreach ($managers as $manager) {
            AppNotification::create([
                'user_id' => $manager->id,
                'tenant_id' => $tenant->id,
                'title' => 'New Maintenance Request',
                'message' => "Tenant {$tenant->name} has submitted a maintenance request: {$validated['title']}",
                'type' => 'maintenance',
                'channel' => 'in_app',
                'related_model' => 'maintenance_requests',
            ]);
        }

        return redirect()->route('portal.dashboard')->with('success', 'Maintenance request submitted.');
    }
}
