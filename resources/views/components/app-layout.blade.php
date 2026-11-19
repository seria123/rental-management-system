@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Rental Management System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900">
        <div class="dashboard-shell">
            @include('layouts.navigation')

            <main class="dashboard-main">
                @if(isset($header))
                    <header class="dashboard-header">
                        <div class="flex items-center justify-between gap-4">
                            <div>{{ $header }}</div>
                            <div class="hidden sm:flex items-center gap-3">
                                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    System healthy
                                </span>
                            </div>
                        </div>
                    </header>
                @endif

                <div class="dashboard-content">
                    @if ($errors->any())
                        <div class="panel p-5 mb-6 border border-red-200 bg-red-50">
                            <ul class="list-disc list-inside text-sm text-red-800 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
