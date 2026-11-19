@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Property details</p>
                <h1 class="page-title">{{ $property->name }}</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('properties.index') }}" class="ghost-button">Back</a>
                <button type="button" class="action-button">
                    @include('layouts.partials.icon', ['name' => 'edit'])
                    Edit property
                </button>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.5fr_0.9fr]">
            <article class="panel overflow-hidden">
                <div class="h-60 bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80');"></div>
                <div class="p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-2xl font-semibold text-slate-900">{{ $property->name }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $property->address }}, {{ $property->city }}</p>
                        </div>
                        <span class="status-badge active">{{ $property->status }}</span>
                    </div>

                    <p class="mt-5 text-sm leading-6 text-slate-600">{{ $property->description ?? 'A premium residential property with stable rental demand and strong operating performance.' }}</p>

                    <div class="mt-6 grid gap-4 md:grid-cols-4">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Units</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $property->units->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Occupied</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $property->units->filter(fn ($unit) => $unit->status === 'occupied' || $unit->activeLease()->exists())->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Vacant</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $property->units->filter(fn ($unit) => $unit->status === 'vacant')->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Income</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900">KSh {{ number_format($property->units->sum(fn ($unit) => (float) ($unit->monthly_rent ?? 0)), 2) }}</p>
                        </div>
                    </div>
                </div>
            </article>

            <aside class="space-y-6">
                <div class="panel p-5">
                    <p class="text-sm font-medium text-slate-500">Recent maintenance</p>
                    @forelse ($property->maintenanceRequests->take(3) as $request)
                        <div class="mt-4 rounded-2xl border border-slate-200 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-semibold text-slate-900">{{ $request->title }}</p>
                                <span class="status-badge {{ $request->status ?? 'pending' }}">{{ $request->status ?? 'pending' }}</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">{{ $request->description }}</p>
                        </div>
                    @empty
                        <p class="mt-4 text-sm text-slate-500">No maintenance requests reported.</p>
                    @endforelse
                </div>

                <div class="panel p-5">
                    <p class="text-sm font-medium text-slate-500">Expenses</p>
                    @forelse ($property->expenses->take(3) as $expense)
                        <div class="mt-4 flex items-center justify-between rounded-2xl border border-slate-200 p-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $expense->category }}</p>
                                <p class="text-xs text-slate-500">{{ $expense->expense_date?->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                            <p class="font-semibold text-slate-900">KSh {{ number_format($expense->amount, 2) }}</p>
                        </div>
                    @empty
                        <p class="mt-4 text-sm text-slate-500">No expenses recorded.</p>
                    @endforelse
                </div>
            </aside>
        </div>

        <section class="panel p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Units</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-900">Occupancy overview</h3>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($property->units as $unit)
                    @php $lease = $unit->activeLease()->first(); @endphp
                    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <p class="text-base font-semibold text-slate-900">Unit {{ $unit->unit_number }}</p>
                                <span class="status-badge {{ $unit->status }}">{{ $unit->status }}</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ $unit->unit_type }} · {{ $unit->floor ? 'Floor ' . $unit->floor : 'Ground floor' }}</p>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-slate-600">
                            <span>Rent: <strong class="text-slate-900">KSh {{ number_format($unit->monthly_rent, 2) }}</strong></span>
                            <span>Tenant: <strong class="text-slate-900">{{ $lease?->tenant?->name ?? 'Vacant' }}</strong></span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                        This property does not have any units configured yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
