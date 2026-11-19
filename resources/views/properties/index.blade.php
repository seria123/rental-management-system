@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Portfolio</p>
                <h1 class="page-title">Properties</h1>
            </div>
            <a href="{{ route('properties.create') }}" class="action-button">
                @include('layouts.partials.icon', ['name' => 'plus'])
                Add property
            </a>
        </div>

        @if ($properties->isEmpty())
            <div class="panel p-10 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                    @include('layouts.partials.icon', ['name' => 'building'])
                </div>
                <h3 class="mt-4 text-xl font-semibold text-slate-900">No properties yet</h3>
                <p class="mt-2 text-sm text-slate-500">Start by adding your first property to manage units, tenants, and rent collection.</p>
            </div>
        @else
            <div class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($properties as $property)
                    @php
                        $units = $property->units;
                        $occupied = $units->filter(fn ($unit) => $unit->status === 'occupied' || $unit->activeLease()->exists())->count();
                        $vacant = $units->filter(fn ($unit) => $unit->status === 'vacant')->count();
                        $income = $units->sum(fn ($unit) => (float) ($unit->monthly_rent ?? 0));
                    @endphp

                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="h-40 bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=900&q=80');"></div>
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">{{ $property->name }}</h2>
                                    <p class="mt-1 text-sm text-slate-500">{{ $property->city }}</p>
                                </div>
                                <span class="status-badge active">{{ $property->status }}</span>
                            </div>

                            <p class="mt-4 text-sm text-slate-600">{{ $property->address }}</p>

                            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-[11px] text-slate-500">
                                <div class="rounded-xl bg-slate-100 px-2 py-2">
                                    <p class="font-semibold text-slate-900">{{ $units->count() }}</p>
                                    <span>Units</span>
                                </div>
                                <div class="rounded-xl bg-slate-100 px-2 py-2">
                                    <p class="font-semibold text-slate-900">{{ $occupied }}</p>
                                    <span>Occupied</span>
                                </div>
                                <div class="rounded-xl bg-slate-100 px-2 py-2">
                                    <p class="font-semibold text-slate-900">{{ $vacant }}</p>
                                    <span>Vacant</span>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4">
                                <div>
                                    <p class="text-xs text-slate-500">Monthly income</p>
                                    <p class="mt-1 text-base font-semibold text-slate-900">KSh {{ number_format($income, 2) }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('properties.show', $property) }}" class="ghost-button px-3 py-2 text-xs">View</a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
