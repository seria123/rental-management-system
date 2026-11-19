@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Inventory</p>
                <h1 class="page-title">Units</h1>
            </div>
            <a href="{{ route('units.create') }}" class="action-button">
                @include('layouts.partials.icon', ['name' => 'plus'])
                Add unit
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($units as $unit)
                @php $tenant = $unit->activeLease()->first()?->tenant; @endphp
                <article class="panel p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $unit->property?->name ?? 'Property' }}</p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-900">Unit {{ $unit->unit_number }}</h2>
                        </div>
                        <span class="status-badge {{ $unit->status }}">{{ $unit->status }}</span>
                    </div>

                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Monthly rent</span>
                            <strong class="text-slate-900">KSh {{ number_format($unit->monthly_rent, 2) }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Type</span>
                            <strong class="text-slate-900">{{ ucfirst(str_replace('_', ' ', $unit->unit_type)) }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Tenant</span>
                            <strong class="text-slate-900">{{ $tenant?->name ?? 'Vacant' }}</strong>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <a href="{{ route('properties.show', $unit->property) }}" class="ghost-button flex-1 justify-center text-xs">View</a>
                        <a href="{{ route('tenants.index') }}" class="action-button flex-1 justify-center text-xs">Manage</a>
                    </div>
                </article>
            @empty
                <div class="col-span-full panel p-10 text-center text-sm text-slate-500">
                    No units have been created yet.
                </div>
            @endforelse
        </div>

        @if ($units->hasPages())
            <div class="panel px-4 py-3">
                {{ $units->links() }}
            </div>
        @endif
    </div>
@endsection
