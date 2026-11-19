<?php

namespace App\Http\Middleware\Filament;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    /**
     * Allow only active administrator users into the panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if (! $user || ! $user->isRole(User::ROLE_ADMIN) || ! $user->canAccessAdminPanel()) {
            abort(403, 'Access denied. Administrator access only.');
        }

        return $next($request);
    }
}
