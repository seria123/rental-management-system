<?php

namespace App\Http\Middleware;

use App\Http\Controllers\UserSessionController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track session after the request is processed
        if ($request->user()) {
            UserSessionController::track($request);
            UserSessionController::updateActivity($request);
        }

        return $response;
    }
}
