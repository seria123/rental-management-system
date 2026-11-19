@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="panel p-8 rounded-3xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Payment Receipt</p>
                <h2 class="text-2xl font-bold text-slate-900">Receipt #{{ $payment->id }}</h2>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold
                {{ $payment->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ ucfirst($payment->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-3">
                <p class="text-sm text-slate-500">Receipt Number</p>
                <p class="text-lg font-semibold text-slate-900">RCP-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="space-y-3">
                <p class="text-sm text-slate-500">Payment Date</p>
                <p class="text-lg font-semibold text-slate-900">{{ $payment->payment_date->format('d M Y, h:i A') }}</p>
            </div>
            <div class="space-y-3">
                <p class="text-sm text-slate-500">Payment Method</p>
                <p class="text-lg font-semibold text-slate-900">{{ ucfirst($payment->payment_method) }}</p>
            </div>
            <div class="space-y-3">
                <p class="text-sm text-slate-500">Transaction Reference</p>
                <p class="text-lg font-semibold text-slate-900">{{ $payment->transaction_reference ?? 'N/A' }}</p>
            </div>
            <div class="space-y-3">
                <p class="text-sm text-slate-500">M-Pesa Receipt</p>
                <p class="text-lg font-semibold text-slate-900">{{ $payment->transaction_id ?? 'Pending' }}</p>
            </div>
            <div class="space-y-3">
                <p class="text-sm text-slate-500">Payment Type</p>
                <p class="text-lg font-semibold text-slate-900">{{ ucfirst($payment->payment_type) }}</p>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-6 mb-8">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-4">Tenant Details</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-slate-500">Name</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $payment->tenant->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Unit</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $payment->lease->unit->unit_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Property</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $payment->lease->unit->property->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Phone</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $payment->phone_number ?? $payment->tenant->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-6 mb-8">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-4">Payment Breakdown</p>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-500 uppercase text-xs border-b border-slate-200">
                        <th class="pb-3 font-semibold">Description</th>
                        <th class="pb-3 font-semibold text-right">Amount (KSh)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-100 py-3">
                        <td class="text-slate-700">Rent Payment - {{ $payment->lease->unit->unit_number }}</td>
                        <td class="text-right text-slate-700 font-medium">{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @if ($payment->rent_invoice_id)
                        <tr class="border-b border-slate-100 py-3">
                            <td class="text-slate-700">Invoice #{{ $payment->rent_invoice_id }} - {{ $payment->rent_invoice?->billing_month->format('F Y') }}</td>
                            <td class="text-right text-slate-700 font-medium">{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="bg-slate-50 py-3">
                        <td class="text-slate-900 font-semibold">Total Paid</td>
                        <td class="text-right text-slate-900 font-bold text-lg">KSh {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($payment->notes)
            <div class="border-t border-slate-200 pt-6">
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-2">Notes</p>
                <p class="text-sm text-slate-600 italic">{{ $payment->notes }}</p>
            </div>
        @endif

        <div class="flex gap-3 pt-6 border-t border-slate-200">
            <button onclick="window.print()"
                class="flex-1 flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                @include('layouts.partials.icon', ['name' => 'printer'])
                Print Receipt
            </button>
            <a href="{{ route('tenant.payment-history') }}"
                class="flex-1 flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-6 py-3 rounded-xl hover:bg-slate-50 transition">
                @include('layouts.partials.icon', ['name' => 'arrow-left'])
                Back to History
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .panel, .panel * {
            visibility: visible;
        }
        .panel {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            border: none !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush