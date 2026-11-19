@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-4">
    <div class="panel p-6 rounded-2xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-semibold
                    {{ $maintenanceRequest->priority === 'urgent' ? 'bg-rose-100 text-rose-700' : ($maintenanceRequest->priority === 'high' ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700') }}">
                    {{ ucfirst($maintenanceRequest->priority) }}
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-semibold
                    {{ $maintenanceRequest->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : ($maintenanceRequest->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600') }}">
                    {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
                </span>
            </div>
            <a href="{{ route('maintenance.index') }}"
                class="text-sm text-indigo-600 hover:underline">
                Back to Maintenance
            </a>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ $maintenanceRequest->title }}</h1>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-slate-50 p-4 rounded-xl">
                <p class="text-sm text-slate-500 uppercase tracking-wider">Unit</p>
                <p class="text-lg font-semibold text-slate-900">{{ $maintenanceRequest->unit->unit_number ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl">
                <p class="text-sm text-slate-500 uppercase tracking-wider">Property</p>
                <p class="text-lg font-semibold text-slate-900">{{ $maintenanceRequest->unit->property->name ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl">
                <p class="text-sm text-slate-500 uppercase tracking-wider">Tenant</p>
                <p class="text-lg font-semibold text-slate-900">{{ $maintenanceRequest->tenant->name ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-8">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-2">Description</p>
            <p class="text-slate-700 whitespace-pre-line">{{ $maintenanceRequest->description }}</p>
        </div>

        <!-- Dates -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-sm text-slate-500 uppercase tracking-wider">Reported At</p>
                <p class="text-lg font-semibold text-slate-900">{{ $maintenanceRequest->reported_at->format('d M Y, h:i A') }}</p>
            </div>
            @if ($maintenanceRequest->resolved_at)
                <div>
                    <p class="text-sm text-slate-500 uppercase tracking-wider">Resolved At</p>
                    <p class="text-lg font-semibold text-emerald-600">{{ $maintenanceRequest->resolved_at->format('d M Y, h:i A') }}</p>
                </div>
            @endif
        </div>

        <!-- Status Update Form (for management/caretaker) -->
        @if (Auth::user()->isManagement() || Auth::user()->hasAnyRole(['caretaker']))
            <div class="border-t border-slate-200 pt-6 mb-6">
                <form method="POST" action="{{ route('maintenance.update', $maintenanceRequest) }}">
                    @csrf
                    @method('PUT')

                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Update Status</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold mb-2">Status</label>
                            <select name="status" class="w-full rounded-xl border-gray-300 px-4 py-3">
                                <option value="open" {{ $maintenanceRequest->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $maintenanceRequest->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="awaiting_parts" {{ $maintenanceRequest->status === 'awaiting_parts' ? 'selected' : '' }}>Awaiting Parts</option>
                                <option value="resolved" {{ $maintenanceRequest->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $maintenanceRequest->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2">Priority</label>
                            <select name="priority" class="w-full rounded-xl border-gray-300 px-4 py-3">
                                <option value="low" {{ $maintenanceRequest->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $maintenanceRequest->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $maintenanceRequest->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $maintenanceRequest->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Assign Caretaker (for admin/property_manager) -->
            @if (Auth::user()->hasAnyRole(['admin', 'property_manager']) && ! $maintenanceRequest->inspector_id)
                <div class="border-t border-slate-200 pt-6">
                    <form method="POST" action="{{ route('maintenance.assign', $maintenanceRequest) }}">
                        @csrf

                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Assign Caretaker</h3>

                        <div class="flex gap-4">
                            <select name="caretaker_id" class="flex-1 rounded-xl border-gray-300 px-4 py-3">
                                <option value="">Select caretaker</option>
                                @foreach($caretakers as $caretaker)
                                    <option value="{{ $caretaker->id }}">{{ $caretaker->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold">
                                Assign
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endif

        <a href="{{ route('maintenance.index') }}"
            class="mt-4 inline-block text-sm text-slate-500 hover:text-indigo-600">
            ← Return to list
        </a>
    </div>
</div>