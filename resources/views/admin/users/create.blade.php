@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-indigo-600">Administration</p>
            <h1 class="page-title">Create User</h1>
        </div>
        <a href="{{ route('admin.users.index') }}" class="ghost-button">Back</a>
    </div>

    <div class="panel p-6">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="admin">Super Admin</option>
                    <option value="owner">Property Owner / Landlord</option>
                    <option value="property_manager">Property Manager / Agent</option>
                    <option value="agent">Agent</option>
                    <option value="caretaker">Caretaker</option>
                    <option value="tenant">Tenant</option>
                    <option value="accountant">Accountant</option>
                    <option value="developer">Developer</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="submit" class="action-button">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
