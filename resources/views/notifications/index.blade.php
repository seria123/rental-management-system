@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Communication</p>
                <h1 class="page-title">Notifications</h1>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="divide-y divide-slate-200">
                @forelse ($notifications as $notification)
                    <div class="flex items-start justify-between gap-4 px-4 py-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $notification->title }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $notification->message }}</p>
                            <p class="mt-2 text-xs text-slate-500">{{ $notification->channel }} · {{ $notification->created_at?->format('d M Y H:i') }}</p>
                        </div>

                        @if (! $notification->is_read)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                <button type="submit" class="rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700">Mark read</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-slate-500">No notifications yet.</div>
                @endforelse
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@endsection
