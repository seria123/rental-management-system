@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-indigo-600">Tenant portal</p>
            <h1 class="page-title">Welcome, {{ $tenant->name }}</h1>
        </div>
        <a href="{{ route('tenant.pay-rent') }}"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold">
            @include('layouts.partials.icon', ['name' => 'credit-card'])
            Pay Rent
        </a>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid gap-6 xl:grid-cols-4">
        <div class="panel p-6 rounded-2xl">
            <p class="text-sm text-slate-500">Current Unit</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $lease?->unit?->unit_number ?? 'No active lease' }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ $lease?->unit?->property?->name ?? 'N/A' }}</p>
        </div>

        <div class="panel p-6 rounded-2xl {{ $outstandingBalance > 0 ? 'bg-rose-50 border-rose-100' : 'bg-emerald-50 border-emerald-100' }}">
            <p class="text-sm text-slate-500">Outstanding Balance</p>
            <h2 class="mt-2 text-xl font-semibold {{ $outstandingBalance > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                KSh {{ number_format($outstandingBalance, 2) }}
            </h2>
            @if ($nextDueDate)
                <p class="mt-1 text-sm text-slate-600">Next due: {{ $nextDueDate->format('d M Y') }}</p>
            @endif
        </div>

        <div class="panel p-6 rounded-2xl bg-indigo-50 border-indigo-100">
            <p class="text-sm text-slate-500">Total Rent Paid</p>
            <h2 class="mt-2 text-xl font-semibold text-indigo-700">
                KSh {{ number_format($totalPaid ?? 0, 2) }}
            </h2>
            <p class="mt-1 text-sm text-slate-600">Confirmed M-Pesa and manual payments.</p>
        </div>

        <div class="panel p-6 rounded-2xl bg-slate-50 border-slate-100">
            <p class="text-sm text-slate-500">Pending Transactions</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-700">{{ $pendingTransactions ?? 0 }}</h2>
            <p class="mt-1 text-sm text-slate-600">Awaiting M-Pesa callback.</p>
        </div>

        <div class="panel p-6 rounded-2xl bg-emerald-50 border-emerald-100">
            <p class="text-sm text-slate-500">Successful Payments</p>
            <h2 class="mt-2 text-xl font-semibold text-emerald-700">{{ $successfulPayments ?? 0 }}</h2>
            <p class="mt-1 text-sm text-slate-600">Completed rent payments.</p>
        </div>

        <div class="panel p-6 rounded-2xl bg-rose-50 border-rose-100">
            <p class="text-sm text-slate-500">Failed Transactions</p>
            <h2 class="mt-2 text-xl font-semibold text-rose-700">{{ $failedTransactions ?? 0 }}</h2>
            <p class="mt-1 text-sm text-slate-600">Cancelled or rejected payments.</p>
        </div>

        <div class="panel p-6 rounded-2xl bg-amber-50 border-amber-100">
            <p class="text-sm text-slate-500">Maintenance Requests</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $maintenance->count() }}</h2>
            <p class="mt-1 text-sm text-slate-600">Open and completed requests.</p>
        </div>

        <div class="panel p-6 rounded-2xl bg-slate-50 border-slate-100">
            <p class="text-sm text-slate-500">Move-Out Requests</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $moveOutRequests->count() }}</h2>
            <p class="mt-1 text-sm text-slate-600">Active requests.</p>
        </div>
    </div>

    <!-- Announcements -->
    @if ($announcements->count() > 0)
        <div class="panel p-6 rounded-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Announcements</h3>
                <a href="{{ route('notifications.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @foreach ($announcements as $announcement)
                    <div class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 shrink-0">
                            @include('layouts.partials.icon', ['name' => 'bell'])
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $announcement->title }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $announcement->content }}</p>
                            <span class="text-xs text-indigo-600">{{ $announcement->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-2">
        <!-- Recent Payments -->
        <div class="panel p-6 rounded-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Recent Payments</h3>
                <a href="{{ route('portal.statements') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($payments as $payment)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $payment->payment_method }}</p>
                            <p class="text-xs text-slate-500">{{ $payment->payment_date?->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold text-slate-900">KSh {{ number_format($payment->amount, 2) }}</span>
                            @if ($payment->transaction_id)
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider mt-0.5">Receipt: {{ $payment->transaction_id }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold
                            {{ $payment->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($payment->status === 'failed' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No payments recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Maintenance Requests -->
        <div class="panel p-6 rounded-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Maintenance Requests</h3>
                <a href="{{ route('portal.maintenance.create') }}" class="text-sm text-indigo-600 hover:underline">Report Issue</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($maintenance as $request)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-sm font-semibold text-slate-900">{{ $request->title }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ ucfirst($request->status) }} · {{ ucfirst($request->priority) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No maintenance requests made yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Move-Out Requests -->
    @if ($moveOutRequests->count() > 0)
        <div class="panel p-6 rounded-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Move-Out Requests</h3>
            </div>
            <div class="space-y-3">
                @foreach ($moveOutRequests as $request)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $request->unit->unit_number ?? 'Unit' }}</p>
                                <p class="text-xs text-slate-500">Move-out: {{ $request->requested_move_out_date->format('d M Y') }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold
                                {{ $request->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($request->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                        @if ($request->reason)
                            <p class="mt-2 text-xs text-slate-500">{{ $request->reason }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>