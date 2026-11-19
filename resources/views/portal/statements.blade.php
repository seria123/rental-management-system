@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Tenant portal</p>
                <h1 class="page-title">Rent statements</h1>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Payment method</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $payment->payment_date?->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $payment->payment_method }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-slate-900">KSh {{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">No payment statement available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
