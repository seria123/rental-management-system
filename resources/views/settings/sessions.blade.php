<x-app-layout title="Active Sessions">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Active Sessions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-900">{{ __('Manage Your Sessions') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('View and terminate active sessions on other devices.') }}</p>
                    </div>
                </div>

                @if ($sessions->isEmpty())
                    <div class="text-center py-8">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-500 mb-3">
                            <i class="fas fa-desktop text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('No active sessions') }}</h3>
                        <p class="text-slate-500">{{ __('You are not currently logged in on any device.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Device</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">IP Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Last Activity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($sessions as $session)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                                    <i class="fas fa-laptop text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $session->is_current ? __('This Device') : __('Other Device') }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $session->user_agent ?? __('Unknown device') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $session->ip_address ?? __('Unknown') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $session->last_activity?->format('d M Y, h:i A') ?? __('Unknown') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-badge {{ $session->is_current ? 'active' : 'pending' }}">
                                                {{ $session->is_current ? __('Current') : __('Active') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if (!$session->is_current)
                                                <form method="POST" action="{{ route('settings.sessions.destroy', $session) }}" onsubmit="return confirm('{{ __('Terminate this session?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-medium underline">
                                                        {{ __('Terminate') }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400">{{ __('Current') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-6 border-t border-slate-200">
                        <form method="POST" action="{{ route('settings.sessions.destroy-all') }}" onsubmit="return confirm('{{ __('Terminate all other sessions? You will remain logged in on this device.') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-rose-600 hover:text-rose-800 font-medium flex items-center gap-2">
                                <i class="fas fa-trash"></i>
                                {{ __('Log Out of All Other Sessions') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
