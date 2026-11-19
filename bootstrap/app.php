<?php

use App\Http\Middleware\EnsurePropertyAccess;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\TrackUserSession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // M-Pesa calls these endpoints server-to-server, so they must bypass CSRF.
        $middleware->validateCsrfTokens(except: [
            'mpesa/callback',
            'api/mpesa/callback',
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'track.session' => TrackUserSession::class,
            'property.access' => EnsurePropertyAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
