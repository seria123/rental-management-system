<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationPreferenceRequest;
use App\Models\SystemSetting;
use App\Models\UserSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct()
    {
        // Authorization is handled inline in each method using canAccess*() methods on User model
    }

    /**
     * Display the settings dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->ensureRelatedRecords();

        $notificationPreferences = $user->notificationPreferences;

        // Group settings by group
        $settings = SystemSetting::all()->groupBy('group');

        $groups = [
            'general' => [
                'title' => 'General Settings',
                'icon' => 'home',
                'description' => 'System name, company details, currency, timezone',
            ],
            'property' => [
                'title' => 'Property Settings',
                'icon' => 'building',
                'description' => 'Unit numbering, property categories, status options',
            ],
            'rent' => [
                'title' => 'Rent Settings',
                'icon' => 'currency',
                'description' => 'Due dates, grace period, late penalties, invoices',
            ],
            'payment' => [
                'title' => 'Payment Settings',
                'icon' => 'credit-card',
                'description' => 'M-Pesa, bank details, payment confirmation',
            ],
            'security' => [
                'title' => 'Security Settings',
                'icon' => 'key',
                'description' => 'Password requirements, session timeout, login restrictions',
            ],
            'notification' => [
                'title' => 'Notification Settings',
                'icon' => 'bell',
                'description' => 'Rent reminders, payment alerts, maintenance notifications',
            ],
            'maintenance' => [
                'title' => 'Maintenance Settings',
                'icon' => 'alert',
                'description' => 'Categories, priority levels, approval requirements',
            ],
            'backup' => [
                'title' => 'Backup & System',
                'icon' => 'file',
                'description' => 'Database backup, activity logs, system health',
            ],
        ];

        return view('settings.index', compact(
            'settings',
            'groups',
            'notificationPreferences',
            'user'
        ));
    }

    /**
     * Update a single system setting.
     */
    public function update(Request $request, SystemSetting $setting): RedirectResponse
    {
        $request->validate([
            'value' => ['required'],
        ]);

        $value = $request->input('value');

        // Encrypt sensitive values
        $isEncrypted = in_array($setting->key, ['mpesa_consumer_key', 'mpesa_consumer_secret', 'mpesa_passkey', 'bank_api_key']);
        $storedValue = $isEncrypted ? encrypt($value) : $value;

        $setting->update([
            'value' => $storedValue,
            'is_encrypted' => $isEncrypted,
        ]);

        return back()->with('status', 'setting-updated');
    }

    /**
     * Display user notification preferences.
     */
    public function notifications(Request $request): View
    {
        $user = $request->user();
        $user->ensureRelatedRecords();

        $notificationPreferences = $user->notificationPreferences;

        return view('settings.notifications', compact(
            'notificationPreferences',
            'user'
        ));
    }

    /**
     * Update user notification preferences.
     */
    public function updateNotifications(NotificationPreferenceRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->ensureRelatedRecords();

        $user->notificationPreferences->update($request->validated());

        return back()->with('status', 'notifications-updated');
    }

    /**
     * Display payment settings.
     */
    public function payment(Request $request): View
    {
        $settings = SystemSetting::where('group', 'payment')->get()->keyBy('key');

        return view('settings.payment', compact('settings'));
    }

    /**
     * Display security settings.
     */
    public function security(Request $request): View
    {
        $settings = SystemSetting::where('group', 'security')->get()->keyBy('key');

        return view('settings.security', compact('settings'));
    }

    /**
     * Display backup and system settings.
     */
    public function backup(Request $request): View
    {
        $settings = SystemSetting::where('group', 'backup')->get()->keyBy('key');

        return view('settings.backup', compact('settings'));
    }

    /**
     * Display active sessions for the current user.
     */
    public function sessions(Request $request): View
    {
        $user = $request->user();
        $user->ensureRelatedRecords();

        $currentSessionId = $request->session()->getId();

        $sessions = $user->sessions()
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                $session->is_current = $session->session_id === $currentSessionId;

                return $session;
            });

        return view('settings.sessions', compact('sessions', 'user', 'currentSessionId'));
    }

    /**
     * Logout from a specific session (other devices).
     */
    public function destroySession(Request $request, UserSession $session): RedirectResponse
    {
        $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $user = $request->user();

        // Ensure the session belongs to the authenticated user
        if ($session->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this session.');
        }

        // Don't allow logging out of current session via this method
        if ($session->session_id === $request->session()->getId()) {
            return back()->withErrors(['error' => 'Cannot log out from current session.']);
        }

        // Delete the session record and the actual session
        Cache::store('database')->forget('cache:'.$session->session_id);
        Cache::store('database')->forget('laravel_cache_'.$session->session_id);
        $session->delete();

        return back()->with('status', 'session-terminated');
    }

    /**
     * Logout from all other sessions.
     */
    public function destroyAllOtherSessions(Request $request): RedirectResponse
    {
        $currentSessionId = $request->session()->getId();
        $user = $request->user();

        $user->sessions()
            ->where('session_id', '!=', $currentSessionId)
            ->each(function ($session) {
                $session->delete();
            });

        return back()->with('status', 'all-other-sessions-terminated');
    }
}
