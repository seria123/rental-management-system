@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <section class="rounded-3xl bg-slate-900 px-6 py-6 text-white shadow-soft md:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.24em] text-slate-300">System administration</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Admin Dashboard</h1>
                <p class="mt-1 max-w-xl text-sm text-slate-300">
                    System-wide overview of all properties, users, and activities across the platform.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('properties.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-white/15">
                    @include('layouts.partials.icon', ['name' => 'building'])
                    Add property
                </a>
                <a href="{{ route('tenants.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400">
                    @include('layouts.partials.icon', ['name' => 'users'])
                    Register tenant
                </a>
                <a href="{{ route('announcements.create') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-100 transition hover:bg-white/5">
                    @include('layouts.partials.icon', ['name' => 'bell'])
                    New announcement
                </a>
            </div>
        </div>
    </section>

    <!-- Stat cards -->
    <section class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <article class="panel stat-card stat-card-indigo">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Total Properties</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $properties->count() }}</h2>
                </div>
                <div class="stat-icon stat-icon-indigo">
                    @include('layouts.partials.icon', ['name' => 'building'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Portfolio under management</p>
        </article>

        <article class="panel stat-card stat-card-emerald">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Total Units</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $units->sum(fn ($u) => $u->activeLease_count ?? 0) }}</h2>
                </div>
                <div class="stat-icon stat-icon-emerald">
                    @include('layouts.partials.icon', ['name' => 'home'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Units across all properties</p>
        </article>

        <article class="panel stat-card stat-card-amber">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Open Maintenance</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $maintenanceRequests }}</h2>
                </div>
                <div class="stat-icon stat-icon-amber">
                    @include('layouts.partials.icon', ['name' => 'wrench'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Maintenance requests awaiting attention</p>
        </article>

        <article class="panel stat-card stat-card-rose">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Open Support Tickets</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $supportTickets }}</h2>
                </div>
                <div class="stat-icon stat-icon-rose">
                    @include('layouts.partials.icon', ['name' => 'chat'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Support tickets awaiting response</p>
        </article>
    </section>

    <!-- Recent activity -->
    <section class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
        <article class="panel p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Recent Activity</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-900">System Activity Log</h3>
                </div>
                <a href="{{ route('admin.activity-log') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">View all</a>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($recentActivity as $activity)
                    <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                            @include('layouts.partials.icon', ['name' => 'clock'])
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $activity->user?->name ?? 'Unknown user' }}</p>
                            <p class="text-xs text-slate-500">{{ $activity->action ?? 'Performed action' }} · {{ $activity->created_at?->format('d M Y H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                        No recent activity recorded.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="panel p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Quick Actions</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-900">Administrative Tools</h3>
                </div>
            </div>

            <div class="mt-5 space-y-2">
                <a href="{{ route('users.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <span>Manage Users</span>
                    @include('layouts.partials.icon', ['name' => 'arrow-right'])
                </a>
                <a href="{{ route('properties.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <span>Manage Properties</span>
                    @include('layouts.partials.icon', ['name' => 'arrow-right'])
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <span>System Settings</span>
                    @include('layouts.partials.icon', ['name' => 'arrow-right'])
                </a>
                <a href="{{ route('admin.activity-log') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <span>Activity Log</span>
                    @include('layouts.partials.icon', ['name' => 'arrow-right'])
                </a>
            </div>
        </article>
    </section>
</div>
@endsection
