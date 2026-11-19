<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use Illuminate\Http\Request;

class UserSessionController extends Controller
{
    /**
     * Record or update the current session for the authenticated user.
     */
    public static function track(Request $request): void
    {
        $user = $request->user();
        if (! $user) {
            return;
        }

        $sessionId = $request->session()->getId();

        // Mark all existing sessions as not current
        $user->sessions()->where('is_current', true)->update(['is_current' => false]);

        // Create or update the current session
        UserSession::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_current' => true,
                'last_activity' => now(),
            ]
        );
    }

    /**
     * Update last activity for the current session.
     */
    public static function updateActivity(Request $request): void
    {
        $user = $request->user();
        if (! $user) {
            return;
        }

        $sessionId = $request->session()->getId();

        UserSession::where('session_id', $sessionId)
            ->where('user_id', $user->id)
            ->update(['last_activity' => now()]);
    }
}
