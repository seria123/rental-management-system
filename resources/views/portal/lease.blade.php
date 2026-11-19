@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">My Lease</p>
            <h1 class="text-2xl font-bold text-slate-900">Lease Agreement</h1>
        </div>
        <a href="{{ route('portal.dashboard') }}"
            class="flex items-center gap-2 text-sm text-indigo-600 hover:underline">
            @include('layouts.partials.icon', ['name' => 'arrow-left'])
            Back to Dashboard
        </a>
    </div>

    @if (!$lease)
        <div class="panel p-6 rounded-2xl border border-rose-200 bg-rose-50 text-center">
            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-rose-100 mx-auto mb-4 text-rose-600">
                @include('layouts.partials.icon', ['name' => 'file-text'])
            </div>
            <h3 class="text-lg font-semibold text-rose-900">No Active Lease</h3>
            <p class="text-sm text-rose-700 mt-1">You do not have an active lease agreement at this time.</p>
        </div>
    @else
        <!-- Lease Overview -->
        <div class="grid gap-6 md:grid-cols-4">
            <div class="panel p-6 rounded-2xl">
                <p class="text-sm text-slate-500">Unit Number</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ $lease->unit->unit_number }}</h3>
            </div>
            <div class="panel p-6 rounded-2xl">
                <p class="text-sm text-slate-500">Property</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $lease->unit->property->name }}</h3>
            </div>
            <div class="panel p-6 rounded-2xl">
                <p class="text-sm text-slate-500">Monthly Rent</p>
                <h3 class="text-2xl font-bold text-slate-900">KSh {{ number_format($lease->monthly_rent, 2) }}</h3>
            </div>
            <div class="panel p-6 rounded-2xl">
                <p class="text-sm text-slate-500">Status</p>
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold
                    {{ $lease->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ ucfirst($lease->status) }}
                </span>
            </div>
        </div>

        <!-- Lease Details -->
        <div class="panel p-6 rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Lease Details</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-slate-500 uppercase tracking-wider">Tenant</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $tenant->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 uppercase tracking-wider">Start Date</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $lease->start_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 uppercase tracking-wider">End Date</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $lease->end_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 uppercase tracking-wider">Deposit Amount</p>
                    <p class="text-lg font-semibold text-slate-900">KSh {{ number_format($lease->deposit_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 uppercase tracking-wider">Payment Due Day</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $lease->payment_due_day }}th of every month</p>
                </div>
                @if ($lease->notes)
                    <div class="md:col-span-2">
                        <p class="text-sm text-slate-500 uppercase tracking-wider">Notes</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $lease->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Lease Agreement Document -->
        @if ($agreement)
            <div class="panel p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Lease Agreement Document</h3>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        @include('layouts.partials.icon', ['name' => 'file-text'])
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $agreement->original_filename }}</p>
                        <p class="text-xs text-slate-500">Lease agreement document</p>
                    </div>
                    <a href="{{ route('portal.lease.download') }}"
                        class="ml-auto flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold">
                        @include('layouts.partials.icon', ['name' => 'download'])
                        Download
                    </a>
                </div>

                <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <p class="text-sm text-slate-500 uppercase tracking-wider mb-2">Signing Status</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Owner Signed</span>
                            <span class="inline-flex items-center gap-1.5
                                {{ $agreement->owner_signed ? 'text-emerald-600' : 'text-rose-600' }} font-medium">
                                @include('layouts.partials.icon', ['name' => 'check'])
                                {{ $agreement->owner_signed ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Tenant Signed</span>
                            <span class="inline-flex items-center gap-1.5
                                {{ $agreement->tenant_signed ? 'text-emerald-600' : 'text-rose-600' }} font-medium">
                                @include('layouts.partials.icon', ['name' => 'check'])
                                {{ $agreement->tenant_signed ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="panel p-6 rounded-2xl border border-rose-200 bg-rose-50">
                <div class="flex items-center gap-3 mb-4">
                    <div class="rounded-full bg-rose-100 p-2 text-rose-600">
                        @include('layouts.partials.icon', ['name' => 'alert-circle'])
                    </div>
                    <h3 class="text-lg font-semibold text-rose-900">No Lease Agreement Uploaded</h3>
                </div>
                <p class="text-sm text-rose-700">Your lease agreement document has not been uploaded yet. Please contact property management.</p>
            </div>
        @endif
    @endif
</div>