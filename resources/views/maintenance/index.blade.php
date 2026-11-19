@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @php
        $user = Auth::user();
        $isTenant = $user->hasAnyRole(['tenant']);
        $isCaretaker = $user->hasAnyRole(['caretaker']);
        $isManagement = $user->isManagement();
    @endphp

    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Maintenance</p>
            <h1 class="text-2xl font-bold text-slate-900">Maintenance Requests</h1>
        </div>

        @if ($isManagement || $isCaretaker)
            <a href="{{ route('maintenance.create') }}"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold">
                @include('layouts.partials.icon', ['name' => 'plus'])
                New Request
            </a>
        @endif

        @if ($isTenant)
            <a href="{{ route('portal.maintenance.create') }}"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold">
                @include('layouts.partials.icon', ['name' => 'plus'])
                Report Issue
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="panel p-4 rounded-2xl">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select name="status" class="w-full rounded-xl border-gray-300 px-4 py-3">
                <option value="">All Statuses</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="awaiting_parts" {{ request('status') === 'awaiting_parts' ? 'selected' : '' }}>Awaiting Parts</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>

            <select name="priority" class="w-full rounded-xl border-gray-300 px-4 py-3">
                <option value="">All Priorities</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>

            @if ($isManagement)
                <select name="caretaker" class="w-full rounded-xl border-gray-300 px-4 py-3">
                    <option value="">All Caretakers</option>
                    @foreach($caretakers as $caretaker)
                        <option value="{{ $caretaker->id }}" {{ request('caretaker') == $caretaker->id ? 'selected' : '' }}>
                            {{ $caretaker->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold">
                Filter
            </button>
        </form>
    </div>

    @if ($maintenanceRequests->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-500 uppercase text-xs border-b border-slate-200">
                        <th class="pb-3 font-semibold">Title</th>
                        <th class="pb-3 font-semibold">Unit</th>
                        <th class="pb-3 font-semibold">Tenant</th>
                        <th class="pb-3 font-semibold">Priority</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Reported</th>
                        <th class="pb-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($maintenanceRequests as $request)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 text-slate-700 font-medium">{{ $request->title }}</td>
                            <td class="py-3 text-slate-700">
                                {{ $request->unit->unit_number ?? 'N/A' }}
                            </td>
                            <td class="py-3 text-slate-700">
                                {{ $request->tenant->name ?? 'N/A' }}
                            </td>
                            <td class="py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-semibold
                                    {{ $request->priority === 'urgent' ? 'bg-rose-100 text-rose-700' : ($request->priority === 'high' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ ucfirst($request->priority) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-semibold
                                    {{ $request->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : ($request->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-500">{{ $request->reported_at->format('d M Y') }}</td>
                            <td class="py-3">
                                <a href="{{ route('maintenance.show', $request) }}"
                                    class="text-sm text-indigo-600 hover:underline">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $maintenanceRequests->links() }}
        </div>
    @else
        <div class="panel p-12 rounded-2xl text-center border border-dashed border-slate-200 bg-slate-50">
            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-slate-200 mx-auto mb-4 text-slate-400">
                @include('layouts.partials.icon', ['name' => 'wrench'])
            </div>
            <h3 class="text-lg font-semibold text-slate-900">No Maintenance Requests</h3>
            <p class="text-sm text-slate-500 mt-1">
                @if ($isTenant)
                    You haven't reported any maintenance issues yet.
                @else
                    No maintenance requests found matching your criteria.
                @endif
            </p>
        </div>
    @endif
</div>