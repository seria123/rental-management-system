@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Security</p>
                <h1 class="page-title">Activity log</h1>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">User</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Event</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Description</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">IP address</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">When</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-900">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <span class="inline-flex rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">{{ $log->event }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $log->description ?? 'No description' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $log->ip_address ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $log->created_at?->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No activity has been recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
