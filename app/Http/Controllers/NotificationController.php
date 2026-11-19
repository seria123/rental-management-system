<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AppNotification::with(['user', 'tenant'])->latest();

        // Filter by user if user_id is set (notifications targeted at a specific user)
        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();
            if ($tenant) {
                $query->where(function ($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id)
                        ->orWhereNull('tenant_id')
                        ->orWhere('user_id', $user->id);
                });
            } else {
                $query->where('user_id', $user->id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        $notifications = $query->paginate(20);

        return view('notifications.index', compact('notifications', 'user'));
    }

    /**
     * Get unread notification count (AJAX endpoint).
     */
    public function unreadCount()
    {
        $user = Auth::user();

        $query = AppNotification::where('is_read', false);

        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();
            if ($tenant) {
                $query->where(function ($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id)
                        ->orWhereNull('tenant_id')
                        ->orWhere('user_id', $user->id);
                });
            } else {
                $query->where('user_id', $user->id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(AppNotification $notification)
    {
        $user = Auth::user();

        // Verify authorization
        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();
            if ($tenant && $notification->tenant_id !== $tenant->id && $notification->user_id !== $user->id) {
                abort(403, 'Unauthorized.');
            }
        } elseif ($notification->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        $notification->update(['is_read' => true]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();

        $query = AppNotification::where('is_read', false);

        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();
            if ($tenant) {
                $query->where(function ($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id)
                        ->orWhereNull('tenant_id')
                        ->orWhere('user_id', $user->id);
                });
            } else {
                $query->where('user_id', $user->id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        $query->update(['is_read' => true, 'read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Store a new notification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'tenant_id' => ['nullable', 'exists:tenants,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['required', 'string', 'max:50'],
            'channel' => ['required', 'in:email,sms,in_app'],
            'related_model' => ['nullable', 'string', 'max:100'],
            'related_id' => ['nullable', 'integer'],
        ]);

        AppNotification::create($validated);

        return back()->with('success', 'Notification queued successfully.');
    }

    /**
     * Create a rent due notification for a tenant.
     */
    public static function notifyRentDue(Tenant $tenant, array $invoiceData): void
    {
        $user = User::where('email', $tenant->email)->first();

        AppNotification::create([
            'user_id' => $user?->id,
            'tenant_id' => $tenant->id,
            'title' => 'Rent Due',
            'message' => 'Rent of KSh '.number_format($invoiceData['amount'], 2).' is due on '.$invoiceData['due_date']->format('d M Y')." for unit {$invoiceData['unit_number']}.",
            'type' => 'rent_due',
            'channel' => 'in_app',
            'related_model' => 'rent_invoices',
            'related_id' => $invoiceData['invoice_id'] ?? null,
        ]);
    }

    /**
     * Create a payment success notification.
     */
    public static function notifyPaymentSuccess(Tenant $tenant, array $paymentData): void
    {
        $user = User::where('email', $tenant->email)->first();

        AppNotification::create([
            'user_id' => $user?->id,
            'tenant_id' => $tenant->id,
            'title' => 'Payment Successful',
            'message' => 'Your payment of KSh '.number_format($paymentData['amount'], 2)." was successful. Receipt: {$paymentData['receipt_number']}.",
            'type' => 'payment_success',
            'channel' => 'in_app',
            'related_model' => 'payments',
            'related_id' => $paymentData['payment_id'] ?? null,
        ]);
    }

    /**
     * Create a payment failure notification.
     */
    public static function notifyPaymentFailed(Tenant $tenant, array $paymentData): void
    {
        $user = User::where('email', $tenant->email)->first();

        AppNotification::create([
            'user_id' => $user?->id,
            'tenant_id' => $tenant->id,
            'title' => 'Payment Failed',
            'message' => 'Your payment of KSh '.number_format($paymentData['amount'], 2)." failed. Reason: {$paymentData['reason']}.",
            'type' => 'payment_failed',
            'channel' => 'in_app',
            'related_model' => 'payments',
            'related_id' => $paymentData['payment_id'] ?? null,
        ]);
    }

    /**
     * Create a lease expiry notification.
     */
    public static function notifyLeaseExpiring(Tenant $tenant, Carbon $expiryDate): void
    {
        $user = User::where('email', $tenant->email)->first();

        AppNotification::create([
            'user_id' => $user?->id,
            'tenant_id' => $tenant->id,
            'title' => 'Lease Expiring Soon',
            'message' => "Your lease expires on {$expiryDate->format('d M Y')}. Please contact property management to renew.",
            'type' => 'lease_expiry',
            'channel' => 'in_app',
        ]);
    }

    /**
     * Create a support ticket reply notification.
     */
    public static function notifySupportReply(int $userId, string $ticketNumber, string $subject): void
    {
        AppNotification::create([
            'user_id' => $userId,
            'title' => 'Support Ticket Reply',
            'message' => "New reply on ticket #{$ticketNumber}: {$subject}",
            'type' => 'support',
            'channel' => 'in_app',
            'related_model' => 'support_tickets',
        ]);
    }

    /**
     * Create a move-out notification.
     */
    public static function notifyMoveOut(int $tenantId, string $title, string $message): void
    {
        AppNotification::create([
            'tenant_id' => $tenantId,
            'title' => $title,
            'message' => $message,
            'type' => 'move_out',
            'channel' => 'in_app',
        ]);
    }

    /**
     * Create a maintenance notification.
     */
    public static function notifyMaintenanceUpdate(int $tenantId, string $title, string $message): void
    {
        AppNotification::create([
            'tenant_id' => $tenantId,
            'title' => $title,
            'message' => $message,
            'type' => 'maintenance',
            'channel' => 'in_app',
        ]);
    }

    /**
     * Create an announcement notification for tenants.
     */
    public static function notifyAnnouncement(array $tenantIds, string $title, string $message): void
    {
        foreach ($tenantIds as $tenantId) {
            AppNotification::create([
                'tenant_id' => $tenantId,
                'title' => $title,
                'message' => $message,
                'type' => 'announcement',
                'channel' => 'in_app',
            ]);
        }
    }
}
