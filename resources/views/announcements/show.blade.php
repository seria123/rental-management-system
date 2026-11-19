@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-4">
    <div class="panel p-8 rounded-2xl">
        <div class="flex items-start justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold
                        {{ $announcement->type === 'maintenance' ? 'bg-amber-100 text-amber-700' : ($announcement->type === 'security' ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700') }}">
                        {{ ucfirst($announcement->type) }}
                    </span>
                    @if ($announcement->is_pinned)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-700">
                            @include('layouts.partials.icon', ['name' => 'pin'])
                            Pinned
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $announcement->title }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    By {{ $announcement->creator?->name ?? 'System' }} on {{ $announcement->created_at->format('d M Y') }}
                </p>
            </div>
            <a href="{{ route('announcements.index') }}"
                class="text-sm text-indigo-600 hover:underline flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'arrow-left'])
                Back to Announcements
            </a>
        </div>

        <div class="prose prose-slate max-w-none">
            {{ $announcement->content }}
        </div>

        @if ($announcement->expires_at)
            <div class="mt-6 p-3 rounded-xl bg-amber-50 border border-amber-100">
                <p class="text-sm text-amber-800">
                    <strong>Expires:</strong> {{ $announcement->expires_at->format('d M Y') }}
                </p>
            </div>
        @endif
    </div>
</div>