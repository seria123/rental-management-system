<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class UserActivityLogger
{
    public function log(User $user, string $event, ?string $description = null, array $details = [], ?Request $request = null): UserActivityLog
    {
        return UserActivityLog::create([
            'user_id' => $user->id,
            'event' => $event,
            'description' => $description,
            'ip_address' => $request?->ip() ?? request()->ip(),
            'user_agent' => $request?->userAgent() ?? request()->userAgent(),
            'details' => $details,
        ]);
    }
}
