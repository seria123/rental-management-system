@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-indigo-600">Administration</p>
            <h1 class="page-title">User Management</h1>
        </div>
        <a href="{{ route('admin.users.create') }}" class="action-button">Add User</a>
    </div>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ ucfirst(str_replace('_',' ',$user->role)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this user?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
