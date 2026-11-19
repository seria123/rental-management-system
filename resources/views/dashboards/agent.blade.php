@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <section class="rounded-3xl bg-slate-900 px-6 py-6 text-white shadow-soft md:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.24em] text-slate-300">Leasing & sales</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Agent Dashboard</h1>
                <p class="mt-1 max-w-xl text-sm text-slate-300">
                    Manage vacancies, lease new units, and track leasing activities across all properties.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('units.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-white/15">
                    @include('layouts.partials.icon', ['name' => 'building'])
                    Add unit
                </a>
                <a href="{{ route('tenants.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400">
                    @include('layouts.partials.icon', ['name' => 'users'])
                    Register tenant
                </a>
            </div>
        </div>
    </section>

    <!-- Stat cards -->
    <section class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <article class="panel stat-card stat-card-indigo">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">All Properties</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $properties->count() }}</h2>
                </div>
                <div class="stat-icon stat-icon-indigo">
                    @include('layouts.partials.icon', ['name' => 'building'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Properties available for leasing</p>
        </article>

        <article class="panel stat-card stat-card-emerald">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Occupied Units</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $occupiedUnits }}</h2>
                </div>
                <div class="stat-icon stat-icon-emerald">
                    @include('layouts.partials.icon', ['name' => 'home'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Currently leased units</p>
        </article>

        <article class="panel stat-card stat-card-amber">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Vacant Units</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $vacantUnits->count() }}</h2>
                </div>
                <div class="stat-icon stat-icon-amber">
                    @include('layouts.partials.icon', ['name' => 'key'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Units available for lease</p>
        </article>

        <article class="panel stat-card stat-card-sky">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Total Units</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $units->count() }}</h2>
                </div>
                <div class="stat-icon stat-icon-sky">
                    @include('layouts.partials.icon', ['name' => 'home'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Total units under management</p>
        </article>
    </section>

    <!-- Vacant units -->
    <section class="panel p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-slate-500">Available Units</p>
                <h3 class="mt-1 text-xl font-semibold text-slate-900">Vacant Units for Lease</h3>
            </div>
            <a href="{{ route('units.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">See all</a>
        </div>

        <div class="mt-5 grid gap-4 lg:grid-cols-3">
            @forelse ($vacantUnits as $unit)
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="h-40 bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1502672260266-1c1efd3e6979?auto=format&fit=crop&w=800&q=80');"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900">{{ $unit->unit_number }}</h4>
                                <p class="mt-1 text-sm text-slate-500">{{ $unit->property->name }}</p>
                            </div>
                            <span class="rounded-full bg-rose-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-rose-700">Vacant</span>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4 text-sm">
                            <div>
                                <p class="text-slate-500">Monthly rent</p>
                                <p class="mt-1 font-semibold text-slate-900">KSh {{ number_format($unit->monthly_rent ?? 0, 2) }}</p>
                            </div>
                            <a href="{{ route('units.show', $unit) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                Lease
                                @include('layouts.partials.icon', ['name' => 'arrow-right'])
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                    No vacant units available at this time.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
