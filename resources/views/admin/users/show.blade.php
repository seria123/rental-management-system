@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-indigo-600">Administration</p>
            <h1 class="page-title">User Details</h1>
        </div>
        <a href="{{ route('admin.users.index') }}" class="ghost-button">Back</a>
    </div>

    <div class="panel p-6">
        <div class="space-y-2">
            <div><strong>Name:</strong> {{ $user->name }}</div>
            <div><strong>Email:</strong> {{ $user->email }}</div>
            <div><strong>Role:</strong> {{ ucfirst(str_replace('_',' ',$user->role)) }}</div>
        </div>
    </div>
</div>
@endsection
