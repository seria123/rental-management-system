@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <section class="rounded-3xl bg-slate-900 px-6 py-6 text-white shadow-soft md:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.24em] text-slate-300">Property ownership</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Owner Dashboard</h1>
                <p class="mt-1 max-w-xl text-sm text-slate-300">
                    Overview of your properties, income, and occupancy across your portfolio.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('properties.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-white/15">
                    @include('layouts.partials.icon', ['name' => 'building'])
                    Add property
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
                    <p class="text-sm text-slate-500">Your Properties</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $properties->count() }}</h2>
                </div>
                <div class="stat-icon stat-icon-indigo">
                    @include('layouts.partials.icon', ['name' => 'building'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Properties you own</p>
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
            <p class="mt-4 text-xs text-slate-500">Currently occupied units</p>
        </article>

        <article class="panel stat-card stat-card-amber">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Vacant Units</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $vacantUnits }}</h2>
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
                    <p class="text-sm text-slate-500">Rent Collected</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">KSh {{ number_format($rentCollected, 2) }}</h2>
                </div>
                <div class="stat-icon stat-icon-sky">
                    @include('layouts.partials.icon', ['name' => 'currency'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Total rent collected</p>
        </article>

        <article class="panel stat-card stat-card-rose">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Outstanding Rent</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">KSh {{ number_format($outstandingRent, 2) }}</h2>
                </div>
                <div class="stat-icon stat-icon-rose">
                    @include('layouts.partials.icon', ['name' => 'alert'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Unpaid rent balances</p>
        </article>
    </section>

    <!-- Properties overview -->
    <section class="panel p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-slate-500">Your Properties</p>
                <h3 class="mt-1 text-xl font-semibold text-slate-900">Portfolio Overview</h3>
            </div>
            <a href="{{ route('properties.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">See all</a>
        </div>

        <div class="mt-5 grid gap-4 lg:grid-cols-3">
            @forelse ($properties->take(6) as $property)
                @php
                    $propertyUnits = $property->units;
                    $occupiedPropertyUnits = $propertyUnits->filter(fn ($unit) => $unit->status === 'occupied' || $unit->activeLease()->exists())->count();
                    $vacantPropertyUnits = $propertyUnits->filter(fn ($unit) => $unit->status === 'vacant')->count();
                    $propertyIncome = $propertyUnits->sum(fn ($unit) => $unit->monthly_rent ?? 0);
                @endphp
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="h-40 bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=900&q=80');"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900">{{ $property->name }}</h4>
                                <p class="mt-1 text-sm text-slate-500">{{ $property->city }}</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-emerald-700">{{ ucfirst($property->status) }}</span>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs text-slate-500">
                            <div class="rounded-xl bg-slate-100 px-2 py-2">
                                <p class="font-semibold text-slate-900">{{ $propertyUnits->count() }}</p>
                                <span>Units</span>
                            </div>
                            <div class="rounded-xl bg-slate-100 px-2 py-2">
                                <p class="font-semibold text-slate-900">{{ $occupiedPropertyUnits }}</p>
                                <span>Occupied</span>
                            </div>
                            <div class="rounded-xl bg-slate-100 px-2 py-2">
                                <p class="font-semibold text-slate-900">{{ $vacantPropertyUnits }}</p>
                                <span>Vacant</span>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4 text-sm">
                            <div>
                                <p class="text-slate-500">Est. monthly income</p>
                                <p class="mt-1 font-semibold text-slate-900">KSh {{ number_format($propertyIncome, 2) }}</p>
                            </div>
                            <a href="{{ route('properties.show', $property) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                View
                                @include('layouts.partials.icon', ['name' => 'arrow-right'])
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                    No properties found. Add a property to start managing your portfolio.
                </div>
            @endforelse
        </div>
    </section>

    <!-- Confirmed Payments -->
    <section class="panel p-6 rounded-2xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Confirmed Rent Payments</p>
                <h3 class="mt-1 text-xl font-semibold text-slate-900">Recent Payments Across Your Properties</h3>
            </div>
            <a href="{{ route('payments.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">See all payments</a>
        </div>

        @if ($confirmedPayments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 uppercase text-xs border-b border-slate-200">
                            <th class="pb-3 font-semibold">Date</th>
                            <th class="pb-3 font-semibold">Tenant</th>
                            <th class="pb-3 font-semibold">Unit / Property</th>
                            <th class="pb-3 font-semibold">Amount</th>
                            <th class="pb-3 font-semibold">Method</th>
                            <th class="pb-3 font-semibold">M-Pesa Receipt</th>
                            <th class="pb-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($confirmedPayments as $payment)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 text-slate-700">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="py-3 text-slate-700">{{ $payment->tenant->name ?? 'Unknown' }}</td>
                                <td class="py-3 text-slate-700 text-sm">
                                    {{ $payment->lease->unit->unit_number ?? 'N/A' }} · {{ $payment->lease->unit->property->name ?? 'N/A' }}
                                </td>
                                <td class="py-3 font-semibold text-slate-900">KSh {{ number_format($payment->amount, 2) }}</td>
                                <td class="py-3 text-slate-700">{{ ucfirst($payment->payment_method) }}</td>
                                <td class="py-3 text-slate-700 text-sm">
                                    {{ $payment->transaction_id ?? '-' }}
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        Completed
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                No confirmed rent payments yet.
            </div>
        @endif
    </section>
</div>
@endsection
