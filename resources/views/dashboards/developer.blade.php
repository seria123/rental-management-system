@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <section class="rounded-3xl bg-slate-900 px-6 py-6 text-white shadow-soft md:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.24em] text-slate-300">System development</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Developer Dashboard</h1>
                <p class="mt-1 max-w-xl text-sm text-slate-300">
                    Technical metrics and system health overview for development and operations.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-white/15">
                    @include('layouts.partials.icon', ['name' => 'gear'])
                    System settings
                </a>
                <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400">
                    @include('layouts.partials.icon', ['name' => 'bell'])
                    Announcements
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
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $properties }}</h2>
                </div>
                <div class="stat-icon stat-icon-indigo">
                    @include('layouts.partials.icon', ['name' => 'building'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Properties in the system</p>
        </article>

        <article class="panel stat-card stat-card-emerald">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Total Units</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $units }}</h2>
                </div>
                <div class="stat-icon stat-icon-emerald">
                    @include('layouts.partials.icon', ['name' => 'home'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Units registered</p>
        </article>

        <article class="panel stat-card stat-card-amber">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Total Users</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $users }}</h2>
                </div>
                <div class="stat-icon stat-icon-amber">
                    @include('layouts.partials.icon', ['name' => 'users'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Registered system users</p>
        </article>

        <article class="panel stat-card stat-card-rose">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-500">Announcements</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $announcements }}</h2>
                </div>
                <div class="stat-icon stat-icon-rose">
                    @include('layouts.partials.icon', ['name' => 'bell'])
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Published announcements</p>
        </article>
    </section>

    <!-- System info -->
    <section class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
        <article class="panel p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">System Health</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-900">System Settings</h3>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">System Settings Count</p>
                        <p class="text-xs text-slate-500">Configuration entries in the database</p>
                    </div>
                    <span class="text-sm font-semibold text-indigo-600">{{ $systemSettings }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">PHP Version</p>
                        <p class="text-xs text-slate-500">Server runtime environment</p>
                    </div>
                    <span class="text-sm font-semibold text-indigo-600">{{ phpversion() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Laravel Version</p>
                        <p class="text-xs text-slate-500">Framework version</p>
                    </div>
                    <span class="text-sm font-semibold text-indigo-600">{{ app()->version() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Environment</p>
                        <p class="text-xs text-slate-500">Current application environment</p>
                    </div>
                    <span class="text-sm font-semibold text-indigo-600">{{ app()->environment() }}</span>
                </div>
            </div>
        </article>

        <article class="panel p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Developer Tools</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-900">Technical Utilities</h3>
                </div>
            </div>

            <div class="mt-5 space-y-2">
                <a href="{{ route('settings.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <span>System Settings</span>
                    @include('layouts.partials.icon', ['name' => 'arrow-right'])
                </a>
                <a href="{{ route('announcements.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <span>Manage Announcements</span>
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
