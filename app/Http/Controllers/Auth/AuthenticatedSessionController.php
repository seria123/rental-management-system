<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        app(UserActivityLogger::class)->log(
            auth()->user(),
            'login',
            'User logged in successfully.',
            ['ip_address' => $request->ip()],
            $request,
        );

        $user = auth()->user();

        $redirect = match ($user->role) {
            'admin' => '/admin',
            'developer' => '/developer/dashboard',
            'owner' => '/owner/dashboard',
            'agent' => '/agent/dashboard',
            'caretaker' => '/caretaker/dashboard',
            'tenant' => '/tenant/dashboard',
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($redirect);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        Auth::guard('web')->logout();

        if ($user) {
            app(UserActivityLogger::class)->log(
                $user,
                'logout',
                'User logged out.',
                ['ip_address' => $request->ip()],
                $request,
            );
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
