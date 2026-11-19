@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <section class="rounded-3xl bg-slate-900 px-6 py-6 text-white shadow-soft md:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.24em] text-slate-300">Property maintenance</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Caretaker Dashboard</h1>
                <p class="mt-1 max-w-xl text-sm text-slate-300">
                    Manage maintenance requests and unit inspections for your assigned properties.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if ($assignedPropertyId)
                    <a href="{{ route('maintenance.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400">
                        @include('layouts.partials.icon', ['name' => 'plus'])
                        New maintenance request
                    </a>
                @endif
            </div>
        </div>
    </section>

    @if (! $assignedPropertyId)
        <div class="panel p-8 rounded-2xl text-center border border-dashed border-slate-200 bg-slate-50">
            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-slate-200 mx-auto mb-4 text-slate-400">
                @include('layouts.partials.icon', ['name' => 'building'])
            </div>
            <h3 class="text-lg font-semibold text-slate-900">No Property Assigned</h3>
            <p class="text-sm text-slate-500 mt-1">
                You haven't been assigned to a specific property yet. Please contact an administrator to be assigned.
            </p>
        </div>
    @else
        <!-- Stat cards -->
        <section class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <article class="panel stat-card stat-card-indigo">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Open Requests</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $maintenanceRequests->count() }}</h2>
                    </div>
                    <div class="stat-icon stat-icon-indigo">
                        @include('layouts.partials.icon', ['name' => 'wrench'])
                    </div>
                </div>
                <p class="mt-4 text-xs text-slate-500">Maintenance requests awaiting attention</p>
            </article>

            <article class="panel stat-card stat-card-emerald">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Units Under Care</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $units->count() }}</h2>
                    </div>
                    <div class="stat-icon stat-icon-emerald">
                        @include('layouts.partials.icon', ['name' => 'home'])
                    </div>
                </div>
                <p class="mt-4 text-xs text-slate-500">Units in your assigned property</p>
            </article>

            <article class="panel stat-card stat-card-amber">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Occupied Units</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $units->filter(fn ($u) => $u->status === 'occupied' || $u->activeLease()->exists())->count() }}</h2>
                    </div>
                    <div class="stat-icon stat-icon-amber">
                        @include('layouts.partials.icon', ['name' => 'home'])
                    </div>
                </div>
                <p class="mt-4 text-xs text-slate-500">Currently occupied units</p>
            </article>
        </section>

        <!-- Maintenance requests -->
        <section class="panel p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Open Maintenance Requests</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-900">Requests Requiring Attention</h3>
                </div>
                <a href="{{ route('maintenance.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">See all</a>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($maintenanceRequests as $request)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                                @include('layouts.partials.icon', ['name' => 'wrench'])
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $request->title }}</p>
                                <p class="text-xs text-slate-500">{{ $request->unit?->property->name ?? 'Property' }} · {{ $request->unit?->unit_number ?? 'Unit' }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold
                            {{ $request->priority === 'high' ? 'bg-rose-100 text-rose-700' : ($request->priority === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                            {{ ucfirst($request->priority ?? 'medium') }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                        No open maintenance requests at this time.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Units overview -->
        <section class="panel p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Units</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-900">Units in Your Property</h3>
                </div>
                <a href="{{ route('units.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">See all</a>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-4">
                @forelse ($units->take(8) as $unit)
                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl mx-auto mb-2
                            {{ $unit->status === 'occupied' || $unit->activeLease()->exists() ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-500' }}">
                            @include('layouts.partials.icon', ['name' => 'home'])
                        </div>
                        <p class="text-sm font-semibold text-slate-900">{{ $unit->unit_number }}</p>
                        <p class="text-xs text-slate-500">
                            {{ $unit->status === 'occupied' || $unit->activeLease()->exists() ? 'Occupied' : 'Vacant' }}
                        </p>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                        No units found in your assigned property.
                    </div>
                @endforelse
            </div>
        </section>
    @endif
</div>
@endsection
