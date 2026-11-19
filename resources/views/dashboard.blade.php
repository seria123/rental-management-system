@extends('layouts.app')

@section('content')
    @php
        $statCards = [
            [
                'label' => 'Properties',
                'value' => $properties->count(),
                'detail' => $properties->count() > 0 ? 'Portfolio active' : 'No properties yet',
                'tone' => 'indigo',
                'icon' => 'building',
            ],
            [
                'label' => 'Occupied units',
                'value' => $occupiedUnits,
                'detail' => $units->count() > 0 ? round(($occupiedUnits / max($units->count(), 1)) * 100) . '% occupancy' : 'No units',
                'tone' => 'emerald',
                'icon' => 'home',
            ],
            [
                'label' => 'Vacant units',
                'value' => $vacantUnits,
                'detail' => $vacantUnits > 0 ? 'Ready for leasing' : 'No vacancies',
                'tone' => 'amber',
                'icon' => 'key',
            ],
            [
                'label' => 'Rent collected',
                'value' => 'KSh ' . number_format($rentCollected, 2),
                'detail' => 'Across all completed payments',
                'tone' => 'sky',
                'icon' => 'currency',
            ],
            [
                'label' => 'Outstanding rent',
                'value' => 'KSh ' . number_format($outstandingRent, 2),
                'detail' => $outstandingInvoices->count() . ' invoices pending',
                'tone' => 'rose',
                'icon' => 'alert',
            ],
        ];
    @endphp

    <div class="space-y-6">
        <section class="rounded-3xl bg-slate-900 px-6 py-6 text-white shadow-soft md:px-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.24em] text-slate-300">Property management</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white">Welcome back, {{ Auth::user()->name }}.</h1>
                    <p class="mt-2 max-w-xl text-sm text-slate-300">
                        Your portfolio is running smoothly. Keep an eye on collections, occupancy trends, and upcoming maintenance requirements.
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
                    <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-100 transition hover:bg-white/5">
                        @include('layouts.partials.icon', ['name' => 'credit-card'])
                        Record payment
                    </a>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @foreach ($statCards as $card)
                <article class="panel stat-card stat-card-{{ $card['tone'] }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                            <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $card['value'] }}</h2>
                        </div>
                        <div class="stat-icon stat-icon-{{ $card['tone'] }}">
                            @include('layouts.partials.icon', ['name' => $card['icon']])
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">{{ $card['detail'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
            <article class="panel p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Monthly rent collection</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-900">Cash flow overview</h3>
                    </div>
                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">+{{ number_format(($monthlyCollection->last()['value'] ?? 0) / max(($monthlyCollection->first()['value'] ?? 1), 1) * 100, 1) }}%</span>
                </div>

                <div class="mt-6 flex h-48 items-end gap-3">
                    @php $maxValue = max($monthlyCollection->pluck('value')->max() ?? 0, 1); @endphp
                    @foreach ($monthlyCollection as $point)
                        <div class="flex flex-1 flex-col items-center gap-3">
                            <div class="flex h-32 w-full items-end justify-center">
                                <div class="w-full rounded-t-2xl bg-gradient-to-t from-indigo-600 to-indigo-400 transition-all duration-300" style="height: {{ max(($point['value'] / $maxValue) * 100, 8) }}%; min-height: 12px;"></div>
                            </div>
                            <span class="text-[11px] font-medium text-slate-500">{{ $point['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Outstanding rent alerts</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-900">At risk</h3>
                    </div>
                    <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">{{ $outstandingInvoices->count() }} open</span>
                </div>

                @forelse ($outstandingInvoices as $invoice)
                    <div class="mt-5 rounded-2xl border border-rose-100 bg-rose-50/60 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $invoice->tenant?->name ?? 'Tenant' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $invoice->lease?->unit?->property?->name ?? 'Property' }} · {{ $invoice->lease?->unit?->unit_number ?? 'Unit' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-rose-700">KSh {{ number_format($invoice->balance, 2) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="mt-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                        No outstanding rent balances right now.
                    </div>
                @endforelse
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_1fr]">
            <article class="panel p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Recent rent payments</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-900">Cash activity</h3>
                    </div>
                    <a href="{{ route('payments.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">View all</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($recentPayments as $payment)
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                                    @include('layouts.partials.icon', ['name' => 'credit-card'])
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $payment->tenant?->name ?? 'Guest tenant' }}</p>
                                    <p class="text-xs text-slate-500">{{ $payment->payment_method }} · {{ $payment->transaction_reference ?? 'No reference' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-900">KSh {{ number_format($payment->amount, 2) }}</p>
                                <p class="text-xs text-slate-500">{{ $payment->payment_date?->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                            No payment activity yet.
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="panel p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Recent expenses</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-900">Operating costs</h3>
                    </div>
                    <a href="{{ route('expenses.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">View all</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($recentExpenses as $expense)
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $expense->category }}</p>
                                <p class="text-xs text-slate-500">{{ $expense->property?->name ?? 'Property' }} · {{ $expense->description }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-900">KSh {{ number_format($expense->amount, 2) }}</p>
                                <p class="text-xs text-slate-500">{{ $expense->expense_date?->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                            No operating expenses logged yet.
                        </div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="panel p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Portfolio</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-900">Properties</h3>
                </div>
                <a href="{{ route('properties.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">See all</a>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-3">
                @forelse ($properties->take(3) as $property)
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
                                <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-emerald-700">{{ $property->status }}</span>
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
                                    <p class="text-slate-500">Monthly income</p>
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
                        No properties have been created yet. Add a property to start managing your portfolio.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
