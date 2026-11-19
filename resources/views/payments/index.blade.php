@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Collections</p>
                <h1 class="page-title">Payments</h1>
            </div>
            <a href="{{ route('payments.create') }}" class="action-button">
                @include('layouts.partials.icon', ['name' => 'plus'])
                Record payment
            </a>
        </div>

        <div class="panel overflow-hidden">
            <div class="grid gap-4 border-b border-slate-200 p-4 md:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Collected</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">KSh {{ number_format((float) $payments->sum('amount'), 2) }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Completed</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $payments->where('status', 'completed')->count() }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Pending</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $payments->where('status', '!=', 'completed')->count() }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Tenant</th>
                            <th class="px-4 py-3 font-medium">Reference</th>
                            <th class="px-4 py-3 font-medium">Amount</th>
                            <th class="px-4 py-3 font-medium">Method</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-900">{{ $payment->tenant?->name ?? 'Tenant' }}</div>
                                    <div class="text-xs text-slate-500">{{ $payment->lease?->unit?->property?->name ?? 'Property' }}</div>
                                </td>
                                <td class="px-4 py-4 text-slate-600">{{ $payment->transaction_reference ?? 'N/A' }}</td>
                                <td class="px-4 py-4 font-semibold text-slate-900">KSh {{ number_format($payment->amount, 2) }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $payment->payment_method }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $payment->payment_date?->format('d M Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-4"><span class="status-badge {{ $payment->status ?? 'pending' }}">{{ $payment->status ?? 'pending' }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                    No payments recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="border-t border-slate-200 px-4 py-4">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
