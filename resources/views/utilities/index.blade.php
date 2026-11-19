@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Operations</p>
                <h1 class="page-title">Utility billing</h1>
            </div>
            <a href="{{ route('utilities.create') }}" class="action-button">Add utility bill</a>
        </div>

        <div class="panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Property</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Tenant</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Period</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($bills as $bill)
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-900 uppercase">{{ $bill->bill_type }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $bill->property?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $bill->tenant?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $bill->billing_period?->format('M Y') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-slate-900">KSh {{ number_format($bill->amount, 2) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700">{{ $bill->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No utility bills recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $bills->links() }}
            </div>
        </div>
    </div>
@endsection
