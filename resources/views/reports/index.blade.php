@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Finance</p>
                <h1 class="page-title">Reports</h1>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="panel p-5">
                <p class="text-sm text-slate-500">Monthly income</p>
                <h2 class="mt-3 text-2xl font-semibold text-slate-900">KSh {{ number_format(
                    \App\Models\Payment::where('status', 'completed')->whereMonth('payment_date', now()->month)->sum('amount'), 2
                ) }}</h2>
            </div>
            <div class="panel p-5">
                <p class="text-sm text-slate-500">Monthly expenses</p>
                <h2 class="mt-3 text-2xl font-semibold text-slate-900">KSh {{ number_format(\App\Models\Expense::whereMonth('expense_date', now()->month)->sum('amount'), 2) }}</h2>
            </div>
            <div class="panel p-5">
                <p class="text-sm text-slate-500">Outstanding rent</p>
                <h2 class="mt-3 text-2xl font-semibold text-slate-900">KSh {{ number_format(\App\Models\RentInvoice::where('status', '!=', 'paid')->sum('balance'), 2) }}</h2>
            </div>
            <div class="panel p-5">
                <p class="text-sm text-slate-500">Vacant units</p>
                <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ \App\Models\Unit::where('status', 'vacant')->count() }}</h2>
            </div>
        </div>
    </div>
@endsection
