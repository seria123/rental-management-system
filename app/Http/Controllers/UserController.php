<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (! Auth::user()->hasAnyRole(['admin', 'developer'])) {
            abort(403);
        }

        $users = User::latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        if (! Auth::user()->hasAnyRole(['admin', 'developer'])) {
            abort(403);
        }

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'developer'])) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'developer'])) {
            abort(403);
        }

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'developer'])) {
            abort(403);
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'developer'])) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|string',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (! Auth::user()->hasAnyRole(['admin', 'developer'])) {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
