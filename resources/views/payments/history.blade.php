@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Payment History</p>
            <h1 class="text-2xl font-bold text-slate-900">My Rent Payments</h1>
        </div>
        <a href="{{ route('tenant.pay-rent') }}"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold">
            @include('layouts.partials.icon', ['name' => 'credit-card'])
            Pay Rent
        </a>
    </div>

    <div class="panel p-6 rounded-2xl">
        @if ($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 uppercase text-xs border-b border-slate-200">
                            <th class="pb-3 font-semibold">Date</th>
                            <th class="pb-3 font-semibold">Unit</th>
                            <th class="pb-3 font-semibold">Amount</th>
                            <th class="pb-3 font-semibold">Method</th>
                            <th class="pb-3 font-semibold">Phone</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">M-Pesa Receipt</th>
                            <th class="pb-3 font-semibold">Reference</th>
                            <th class="pb-3 font-semibold">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($payments as $payment)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 text-slate-700">{{ $payment->payment_date->format('d M Y, h:i A') }}</td>
                                <td class="py-3 text-slate-700">
                                    {{ $payment->lease->unit->unit_number ?? 'N/A' }}
                                </td>
                                <td class="py-3 font-semibold text-slate-900">
                                    KSh {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="py-3 text-slate-700">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs bg-slate-100 text-slate-600">
                                        @include('layouts.partials.icon', ['name' => 'phone'])
                                        {{ ucfirst($payment->payment_method) }}
                                    </span>
                                </td>
                                <td class="py-3 text-slate-700 text-sm">
                                    {{ $payment->phone_number ?? '-' }}
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ $payment->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($payment->status === 'failed' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-slate-700 text-sm">
                                    {{ $payment->transaction_id ?? '-' }}
                                </td>
                                <td class="py-3 text-slate-700">
                                    {{ $payment->transaction_reference ?? '-' }}
                                </td>
                                <td class="py-3">
                                    @if ($payment->status === 'completed')
                                        <a href="{{ route('tenant.payment-receipt', $payment) }}"
                                            class="text-xs text-indigo-600 hover:underline font-medium">
                                            View Receipt
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $payments->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 mx-auto mb-4">
                    @include('layouts.partials.icon', ['name' => 'credit-card'])
                </div>
                <h3 class="text-lg font-semibold text-slate-900">No payments yet</h3>
                <p class="text-sm text-slate-500 mt-1">You haven't made any rent payments yet.</p>
                <a href="{{ route('tenant.pay-rent') }}"
                    class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold">
                    Pay Rent Now
                </a>
            </div>
        @endif
    </div>
</div>