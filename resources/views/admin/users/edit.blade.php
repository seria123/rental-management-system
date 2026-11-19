@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-indigo-600">Administration</p>
            <h1 class="page-title">Edit User</h1>
        </div>
        <a href="{{ route('admin.users.index') }}" class="ghost-button">Back</a>
    </div>

    <div class="panel p-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>
            <div>
                <label class="form-label">Password (leave blank to keep)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div>
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="admin" @selected($user->role=='admin')>Super Admin</option>
                    <option value="owner" @selected($user->role=='owner')>Property Owner / Landlord</option>
                    <option value="property_manager" @selected($user->role=='property_manager')>Property Manager / Agent</option>
                    <option value="agent" @selected($user->role=='agent')>Agent</option>
                    <option value="caretaker" @selected($user->role=='caretaker')>Caretaker</option>
                    <option value="tenant" @selected($user->role=='tenant')>Tenant</option>
                    <option value="accountant" @selected($user->role=='accountant')>Accountant</option>
                    <option value="developer" @selected($user->role=='developer')>Developer</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="submit" class="action-button">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
