@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @php
        $user = Auth::user();
        $isManagement = $user->isManagement();
        $isTenant = $user->hasAnyRole(['tenant']);
    @endphp

    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Announcements</p>
            <h1 class="text-2xl font-bold text-slate-900">Property Announcements</h1>
        </div>

        @if ($isManagement)
            <a href="{{ route('announcements.create') }}"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold">
                @include('layouts.partials.icon', ['name' => 'bell'])
                Publish Announcement
            </a>
        @endif
    </div>

    @if ($announcements->count() > 0)
        <div class="grid gap-4">
            @foreach ($announcements as $announcement)
                <div class="panel p-6 rounded-2xl border border-slate-200 {{ $announcement->is_pinned ? 'bg-amber-50 border-amber-100' : '' }}">
                    <div class="flex items-start justify-between gap-4 mb-3">
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
                            <h3 class="text-lg font-semibold text-slate-900">{{ $announcement->title }}</h3>
                            <p class="text-sm text-slate-500 mt-1">{{ $announcement->content }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-400">{{ $announcement->created_at->format('d M Y') }}</p>
                            @if ($announcement->expires_at)
                                <p class="text-xs text-slate-400">Expires: {{ $announcement->expires_at->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-slate-500">
                        <span>By: {{ $announcement->creator?->name ?? 'System' }}</span>
                        <span>•</span>
                        <span>{{ ucfirst($announcement->target_type) }} target</span>
                        @if ($announcement->property)
                            <span>•</span>
                            <span>{{ $announcement->property->name }}</span>
                        @endif
                    </div>

                    @if ($isManagement)
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('announcements.edit', $announcement) }}"
                                class="text-sm text-indigo-600 hover:underline px-3 py-1">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-sm text-rose-600 hover:underline px-3 py-1">
                                Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
    @else
        <div class="panel p-12 rounded-2xl text-center border border-dashed border-slate-200 bg-slate-50">
            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-slate-200 mx-auto mb-4 text-slate-400">
                @include('layouts.partials.icon', ['name' => 'bell'])
            </div>
            <h3 class="text-lg font-semibold text-slate-900">No Announcements</h3>
            <p class="text-sm text-slate-500 mt-1">
                @if ($isTenant)
                    You don't have any announcements at this time.
                @else
                    No announcements have been published yet. Create your first announcement.
                @endif
            </p>
            @if ($isManagement)
                <a href="{{ route('announcements.create') }}"
                    class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold">
                    Publish Announcement
                </a>
            @endif
        </div>
    @endif
</div>