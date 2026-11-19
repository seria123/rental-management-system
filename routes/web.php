<?php

use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LeaseAgreementController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\MoveOutRequestController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UtilityController;
use App\Models\Announcement;
use App\Models\Expense;
use App\Models\MaintenanceRequest;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentInvoice;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Services\MpesaService;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    // Determine accessible properties based on role
    if ($user->hasAnyRole(['admin', 'developer'])) {
        $properties = Property::with(['units', 'units.activeLease.tenant'])->get();
        $units = Unit::with(['property', 'activeLease.tenant'])->get();
        $paymentQuery = Payment::with(['tenant', 'lease.unit.property']);
        $invoiceQuery = RentInvoice::with(['tenant', 'lease.unit.property']);
        $expenseQuery = Expense::with(['property', 'unit']);
    } elseif ($user->hasAnyRole(['owner'])) {
        $properties = Property::with(['units', 'units.activeLease.tenant'])
            ->where('property_owner_id', $user->id)
            ->get();
        $units = Unit::with(['property', 'activeLease.tenant'])
            ->whereHas('property', function ($q) use ($user) {
                $q->where('property_owner_id', $user->id);
            })
            ->get();
        $paymentQuery = Payment::with(['tenant', 'lease.unit.property'])
            ->whereHas('lease.unit.property', function ($q) use ($user) {
                $q->where('property_owner_id', $user->id);
            });
        $invoiceQuery = RentInvoice::with(['tenant', 'lease.unit.property'])
            ->whereHas('lease.unit.property', function ($q) use ($user) {
                $q->where('property_owner_id', $user->id);
            });
        $expenseQuery = Expense::with(['property', 'unit'])
            ->where('property_id', function ($q) use ($user) {
                $q->select('id')->from('properties')->where('property_owner_id', $user->id);
            });
    } else {
        // Agents, caretakers, property managers - see assigned properties
        $profile = $user->profile;
        $assignedPropertyId = $profile?->assigned_property;

        if ($assignedPropertyId) {
            $properties = Property::with(['units', 'units.activeLease.tenant'])
                ->where('id', $assignedPropertyId)
                ->get();
            $units = Unit::with(['property', 'activeLease.tenant'])
                ->where('property_id', $assignedPropertyId)
                ->get();
        } else {
            $properties = collect();
            $units = collect();
        }
        $paymentQuery = Payment::with(['tenant', 'lease.unit.property'])->limit(0);
        $invoiceQuery = RentInvoice::with(['tenant', 'lease.unit.property'])->limit(0);
        $expenseQuery = Expense::with(['property', 'unit'])->limit(0);
    }

    $recentPayments = $paymentQuery->latest('payment_date')->take(5)->get();
    $outstandingInvoices = $invoiceQuery
        ->where('balance', '>', 0)
        ->orderByDesc('balance')
        ->take(5)
        ->get();
    $recentExpenses = $expenseQuery->latest('expense_date')->take(5)->get();

    $occupiedUnits = $units->filter(fn (Unit $unit) => $unit->status === 'occupied' || $unit->activeLease()->exists())->count();
    $vacantUnits = $units->filter(fn (Unit $unit) => $unit->status === 'vacant')->count();
    $rentCollected = (float) (clone $paymentQuery)->where('status', 'completed')->sum('amount');
    $outstandingRent = (float) $invoiceQuery->where('status', '!=', 'paid')->sum('balance');

    $monthlyCollection = collect(range(5, 0))->map(function ($offset) use ($paymentQuery) {
        $month = now()->subMonths($offset);

        return [
            'label' => $month->shortMonthName,
            'value' => (float) $paymentQuery->where('status', 'completed')
                ->whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->sum('amount'),
        ];
    });

    return view('dashboard', compact(
        'properties',
        'units',
        'recentPayments',
        'outstandingInvoices',
        'recentExpenses',
        'occupiedUnits',
        'vacantUnits',
        'rentCollected',
        'outstandingRent',
        'monthlyCollection',
        'user'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:developer'])->group(function () {
    Route::get('/developer/dashboard', function () {
        $user = Auth::user();
        $properties = Property::count();
        $units = Unit::count();
        $users = User::count();
        $announcements = Announcement::count();
        $systemSettings = SystemSetting::count();

        return view('dashboards.developer', compact('properties', 'units', 'users', 'announcements', 'systemSettings'));
    })->name('developer.dashboard');
});
Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/owner/dashboard', function () {
        $user = Auth::user();
        $properties = Property::with(['units', 'units.activeLease.tenant'])
            ->where('property_owner_id', $user->id)
            ->get();
        $units = Unit::with(['property', 'activeLease.tenant'])
            ->whereHas('property', function ($q) use ($user) {
                $q->where('property_owner_id', $user->id);
            })
            ->get();
        $occupiedUnits = $units->filter(fn ($unit) => $unit->status === 'occupied' || $unit->activeLease()->exists())->count();
        $vacantUnits = $units->filter(fn ($unit) => $unit->status === 'vacant')->count();
        $rentCollected = (float) Payment::with(['tenant', 'lease.unit.property'])
            ->whereHas('lease.unit.property', function ($q) use ($user) {
                $q->where('property_owner_id', $user->id);
            })
            ->where('status', 'completed')
            ->sum('amount');
        $outstandingRent = (float) RentInvoice::with(['tenant', 'lease.unit.property'])
            ->whereHas('lease.unit.property', function ($q) use ($user) {
                $q->where('property_owner_id', $user->id);
            })
            ->where('status', '!=', 'paid')
            ->sum('balance');

        // All confirmed payments for the owner's properties
        $confirmedPayments = Payment::with(['tenant', 'lease.unit.property'])
            ->whereHas('lease.unit.property', function ($q) use ($user) {
                $q->where('property_owner_id', $user->id);
            })
            ->where('status', 'completed')
            ->latest('payment_date')
            ->take(15)
            ->get();

        return view('dashboards.owner', compact('properties', 'units', 'occupiedUnits', 'vacantUnits', 'rentCollected', 'outstandingRent', 'confirmedPayments'));
    })->name('owner.dashboard');
});
Route::middleware(['auth', 'role:agent'])->group(function () {
    Route::get('/agent/dashboard', function () {
        $user = Auth::user();
        $properties = Property::all();
        $vacantUnits = Unit::where('status', 'vacant')->with('property')->get();
        $units = Unit::with(['property', 'activeLease.tenant'])->get();
        $occupiedUnits = $units->filter(fn ($unit) => $unit->status === 'occupied' || $unit->activeLease()->exists())->count();

        return view('dashboards.agent', compact('properties', 'vacantUnits', 'units', 'occupiedUnits'));
    })->name('agent.dashboard');
});
Route::middleware(['auth', 'role:caretaker'])->group(function () {
    Route::get('/caretaker/dashboard', function () {
        $user = Auth::user();
        $profile = $user->profile;
        $assignedPropertyId = $profile?->assigned_property;

        $maintenanceRequests = MaintenanceRequest::with(['unit.property', 'tenant'])
            ->where('status', 'open')
            ->when($assignedPropertyId, function ($q) use ($assignedPropertyId) {
                $q->whereHas('unit.property', function ($q2) use ($assignedPropertyId) {
                    $q2->where('id', $assignedPropertyId);
                });
            })
            ->latest()
            ->take(10)
            ->get();

        $units = Unit::with(['property', 'activeLease.tenant'])
            ->when($assignedPropertyId, function ($q) use ($assignedPropertyId) {
                $q->where('property_id', $assignedPropertyId);
            })
            ->get();

        return view('dashboards.caretaker', compact('maintenanceRequests', 'units', 'assignedPropertyId'));
    })->name('caretaker.dashboard');
});
Route::middleware(['auth', 'role:tenant'])->group(function () {
    Route::get('/tenant/dashboard', function () {
        return redirect()->route('portal.dashboard');
    })->name('tenant.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('properties', PropertyController::class);

    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');

    Route::get('/units', function () {
        $units = Unit::with(['property', 'activeLease.tenant'])->latest()->paginate(10);

        return view('units.index', compact('units'));
    })->name('units.index');

    Route::get('/units/create', function () {
        $properties = Property::with('units')->get();

        return view('units.create', compact('properties'));
    })->name('units.create');

    Route::post('/units', function (Request $request) {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'unit_number' => 'required|string|max:50',
            'unit_type' => 'required|string|max:50',
            'floor' => 'nullable|string|max:20',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'status' => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
        ]);

        $unitData = $validated;
        if ($request->hasFile('lease_agreement_file')) {
            $file = $request->file('lease_agreement_file');
            $fileName = 'unit_lease_agreement_'.$validated['property_id'].'_'.$validated['unit_number'].'_'.str_replace(' ', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('private/lease_agreements', $fileName);
            $unitData['lease_agreement_path'] = $filePath;
            $unitData['lease_agreement_filename'] = $file->getClientOriginalName();
        }

        Unit::create($unitData);

        return redirect()->route('units.index')->with('success', 'Unit created successfully.');
    })->name('units.store');

    Route::get('/payments', function () {
        $payments = Payment::with(['tenant', 'lease.unit.property'])->latest('payment_date')->paginate(10);

        return view('payments.index', compact('payments'));
    })->name('payments.index');

    Route::get('/payments/create', function () {
        $tenants = Tenant::with(['currentLease.unit.property'])->get();

        return view('payments.create', compact('tenants'));
    })->name('payments.create');

    Route::post('/payments', function (Request $request, MpesaService $mpesa) {
        $rules = [
            'tenant_id' => 'required|exists:tenants,id',
            'lease_id' => 'nullable|exists:leases,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'phone_number' => 'nullable|string|max:20',
            'transaction_reference' => 'nullable|string|max:100',
            'payment_type' => 'required|string|max:50',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ];

        if ($request->input('payment_method') === 'Mpesa') {
            $rules['phone_number'] = 'required|string|max:20';
        }

        $validated = $request->validate($rules);

        if (($validated['payment_method'] ?? '') === 'Mpesa' && ! empty($validated['phone_number'])) {
            if ($mpesa->hasCredentials()) {
                try {
                    $response = $mpesa->stkPush(
                        phone: $validated['phone_number'],
                        amount: (float) $validated['amount'],
                        accountReference: $validated['transaction_reference'] ?: 'RENT-'.($validated['tenant_id'] ?? 'PAYMENT'),
                        description: ucfirst((string) $validated['payment_type']).' payment',
                    );

                    $validated['status'] = 'pending';

                    // Store transaction
                    MpesaTransaction::create([
                        'tenant_id' => $validated['tenant_id'],
                        'amount' => $validated['amount'],
                        'transaction_type' => 'stk_push',
                        'status' => 'initiated',
                        'checkout_request_id' => $response['CheckoutRequestID'] ?? null,
                        'merchant_request_id' => $response['MerchantRequestID'] ?? null,
                        'payload' => [
                            'phone_number' => $validated['phone_number'],
                            'response' => $response,
                            'requested_at' => now()->toDateTimeString(),
                        ],
                    ]);
                } catch (Throwable $e) {
                    $validated['status'] = 'failed';
                    $validated['notes'] = trim(($validated['notes'] ?? '').' M-Pesa error: '.$e->getMessage());
                }
            } else {
                $validated['status'] = 'failed';
                $validated['notes'] = trim(($validated['notes'] ?? '').' M-Pesa credentials are not configured.');
            }
        }

        $payment = Payment::create($validated);

        // Update rent invoice balance when payment is for rent
        if ($validated['payment_type'] === 'rent' && $validated['lease_id']) {
            $invoice = RentInvoice::where('lease_id', $validated['lease_id'])
                ->where('status', '!=', 'paid')
                ->orderBy('billing_month', 'asc')
                ->first();

            if ($invoice) {
                $newAmountPaid = (float) $invoice->amount_paid + (float) $validated['amount'];
                $newBalance = max(0, (float) $invoice->amount - $newAmountPaid);

                $invoice->update([
                    'amount_paid' => $newAmountPaid,
                    'balance' => $newBalance,
                    'status' => $newBalance <= 0 ? 'paid' : 'unpaid',
                ]);
            }
        }

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    })->name('payments.store');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    Route::get('/admin/activity-log', function () {
        $logs = UserActivityLog::with('user')->latest()->paginate(20);

        return view('admin.activity-log', compact('logs'));
    })->middleware('role:admin')->name('admin.activity-log');

    // User management is handled by Filament admin panel at /admin/users

    Route::get('/utilities', [UtilityController::class, 'index'])->name('utilities.index');
    Route::get('/utilities/create', [UtilityController::class, 'create'])->name('utilities.create');
    Route::post('/utilities', [UtilityController::class, 'store'])->name('utilities.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::post('/mpesa/stk-push', [MpesaController::class, 'stkPush'])->name('mpesa.stk-push');

    Route::get('/portal/dashboard', [PortalController::class, 'dashboard'])->name('portal.dashboard');
    Route::get('/portal/statements', [PortalController::class, 'statements'])->name('portal.statements');
    Route::get('/portal/lease', [PortalController::class, 'lease'])->name('portal.lease');
    Route::get('/portal/lease/download', [PortalController::class, 'downloadLease'])->name('portal.lease.download');
    Route::get('/portal/maintenance/create', [PortalController::class, 'maintenanceCreate'])->name('portal.maintenance.create');
    Route::post('/portal/maintenance', [PortalController::class, 'maintenanceStore'])->name('portal.maintenance.store');

    // Lease Agreement routes
    Route::get('/leases/{lease}/agreements', [LeaseAgreementController::class, 'show'])->name('leases.agreements.show');
    Route::post('/leases/{lease}/agreements', [LeaseAgreementController::class, 'store'])->name('leases.agreements.store');
    Route::post('/lease-agreements/{agreement}/sign', [LeaseAgreementController::class, 'sign'])->name('lease-agreements.sign');
    Route::get('/lease-agreements/{agreement}/download', [LeaseAgreementController::class, 'download'])->name('lease-agreements.download');

    // Move-Out Request routes
    Route::get('/tenants/{tenant}/move-out/create', [MoveOutRequestController::class, 'create'])->name('tenants.move-out.create');
    Route::post('/tenants/{tenant}/move-out', [MoveOutRequestController::class, 'store'])->name('tenants.move-out.store');
    Route::get('/move-out-requests', [MoveOutRequestController::class, 'index'])->name('move-out-requests.index');
    Route::get('/move-out-requests/upcoming', [MoveOutRequestController::class, 'upcoming'])->name('move-out-requests.upcoming');
    Route::get('/move-out-requests/{moveOutRequest}/process', [MoveOutRequestController::class, 'processEdit'])->name('move-out-requests.process');
    Route::post('/move-out-requests/{moveOutRequest}/process', [MoveOutRequestController::class, 'process'])->name('move-out-requests.process.submit');
    Route::post('/move-out-requests/{moveOutRequest}/approve', [MoveOutRequestController::class, 'approve'])->name('move-out-requests.approve');
    Route::post('/move-out-requests/{moveOutRequest}/reject', [MoveOutRequestController::class, 'reject'])->name('move-out-requests.reject');
    Route::post('/move-out-requests/{moveOutRequest}/schedule-inspection', [MoveOutRequestController::class, 'scheduleInspection'])->name('move-out-requests.schedule-inspection');
    Route::post('/move-out-requests/{moveOutRequest}/complete-inspection', [MoveOutRequestController::class, 'completeInspection'])->name('move-out-requests.complete-inspection');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/two-factor', [ProfileController::class, 'toggleTwoFactor'])->name('profile.two-factor');
    Route::get('/profile/picture/download', [ProfileController::class, 'downloadPicture'])->name('profile.picture.download');
    Route::delete('/profile/picture', [ProfileController::class, 'deletePicture'])->name('profile.picture.delete');

    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/notifications', [SettingsController::class, 'notifications'])->name('settings.notifications');
    Route::patch('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::get('/settings/payment', [SettingsController::class, 'payment'])->name('settings.payment');
    Route::get('/settings/security', [SettingsController::class, 'security'])->name('settings.security');
    Route::get('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::patch('/settings/{setting}', [SettingsController::class, 'update'])->name('settings.update');

    // Announcement routes
    Route::get('/announcements', [AnnouncementsController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AnnouncementsController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementsController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}', [AnnouncementsController::class, 'show'])->name('announcements.show');
    Route::get('/announcements/{announcement}/edit', [AnnouncementsController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AnnouncementsController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementsController::class, 'destroy'])->name('announcements.destroy');

    // User Session routes
    Route::get('/settings/sessions', [SettingsController::class, 'sessions'])->name('settings.sessions');
    Route::delete('/settings/sessions/{session}', [SettingsController::class, 'destroySession'])->name('settings.sessions.destroy');
    Route::delete('/settings/sessions/all-others', [SettingsController::class, 'destroyAllOtherSessions'])->name('settings.sessions.destroy-all');

    // Tenant payment routes
    Route::get('/tenant/pay-rent', [PaymentController::class, 'payRent'])->name('tenant.pay-rent');
    Route::post('/tenant/pay-rent/stk-push', [PaymentController::class, 'payRentStkPush'])->name('tenant.pay-rent.stk-push');
    Route::get('/tenant/payment-status', [PaymentController::class, 'queryStkPushStatus'])->name('tenant.payment-status');
    Route::get('/tenant/payment-history', [PaymentController::class, 'paymentHistory'])->name('tenant.payment-history');
    Route::get('/tenant/payment-receipt/{payment}', [PaymentController::class, 'receipt'])->name('tenant.payment-receipt');
    Route::get('/tenant/payment-receipt/{payment}/download', [PaymentController::class, 'downloadReceipt'])->name('tenant.payment-receipt.download');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('support', SupportController::class);
    Route::post('/support/{ticket}/replies', [SupportController::class, 'storeReply'])->name('support.replies.store');

    // Maintenance Request routes
    Route::get('/maintenance', [MaintenanceRequestController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceRequestController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [MaintenanceRequestController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{maintenanceRequest}', [MaintenanceRequestController::class, 'show'])->name('maintenance.show');
    Route::put('/maintenance/{maintenanceRequest}', [MaintenanceRequestController::class, 'update'])->name('maintenance.update');
    Route::post('/maintenance/{maintenanceRequest}/assign', [MaintenanceRequestController::class, 'assign'])->name('maintenance.assign');
    Route::delete('/maintenance/{maintenanceRequest}', [MaintenanceRequestController::class, 'destroy'])->name('maintenance.destroy');
});

// Public M-Pesa callbacks (no auth, no CSRF)
Route::post('/mpesa/callback', [PaymentController::class, 'handleCallback'])->withoutMiddleware([VerifyCsrfToken::class]);
Route::post('/api/mpesa/callback', [PaymentController::class, 'handleCallback'])->withoutMiddleware([VerifyCsrfToken::class]);

require __DIR__.'/auth.php';
