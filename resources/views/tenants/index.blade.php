@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Residents</p>
                <h1 class="page-title">Tenants</h1>
            </div>
            <a href="{{ route('tenants.create') }}" class="action-button">
                @include('layouts.partials.icon', ['name' => 'plus'])
                Register tenant
            </a>
        </div>

        <div class="panel overflow-hidden">
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 md:flex-row md:items-center md:justify-between">
                <div class="relative w-full max-w-md">
                    <input type="search" placeholder="Search tenant or phone" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none">
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                        @include('layouts.partials.icon', ['name' => 'filter'])
                        Filter
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Tenant</th>
                            <th class="px-4 py-3 font-medium">Property</th>
                            <th class="px-4 py-3 font-medium">Unit</th>
                            <th class="px-4 py-3 font-medium">Monthly rent</th>
                            <th class="px-4 py-3 font-medium">Outstanding</th>
                            <th class="px-4 py-3 font-medium">Lease</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($tenants as $tenant)
                            @php $currentLease = $tenant->currentLease()->first(); @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-900">{{ $tenant->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $tenant->email ?? 'No email' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $tenant->phone ?? 'No phone' }}</div>
                                </td>
                                <td class="px-4 py-4 text-slate-600">{{ $currentLease?->unit?->property?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $currentLease?->unit?->unit_number ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-slate-600">KSh {{ number_format((float) ($currentLease?->monthly_rent ?? 0), 2) }}</td>
                                <td class="px-4 py-4 font-medium text-rose-600">
                                    KSh {{ number_format((float) ($currentLease?->invoices()->where('status', '!=', 'paid')->sum('balance') ?? 0), 2) }}
                                </td>
                                <td class="px-4 py-4 text-slate-600">{{ $currentLease?->status ?? 'No lease' }}</td>
                                <td class="px-4 py-4">
                                    <span class="status-badge {{ $tenant->status ?? 'active' }}">{{ $tenant->status ?? 'active' }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $activeLease = $tenant->leases->where('status', 'active')->first();
                                        $pendingMoveOut = $tenant->moveOutRequests->where('status', 'pending')->first();
                                    @endphp
                                    @if ($activeLease && ! $pendingMoveOut)
                                        <a href="{{ route('tenants.move-out.create', $tenant->id) }}"
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('Submit move-out request for this tenant?')">
                                            <i class="fas fa-sign-out-alt me-1"></i> Move-Out
                                        </a>
                                    @elseif ($pendingMoveOut)
                                        <button class="btn btn-outline-warning btn-sm disabled" title="Move-out request pending">
                                            <i class="fas fa-clock me-1"></i> Pending
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">
                                    No tenants have been registered yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tenants->hasPages())
                <div class="border-t border-slate-200 px-4 py-4">
                    {{ $tenants->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
