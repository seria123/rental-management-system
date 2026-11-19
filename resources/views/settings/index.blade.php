<x-app-layout title="System Settings">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-900">{{ __('Settings Overview') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Configure system-wide preferences and policies.') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($groups as $groupKey => $group)
                        <a href="{{ $groupKey === 'payment' ? route('settings.payment') : ($groupKey === 'security' ? route('settings.security') : ($groupKey === 'backup' ? route('settings.backup') : '#')) }}"
                           class="group block p-5 rounded-xl border border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                                    @include('layouts.partials.icon', ['name' => $group['icon']])
                                </div>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">{{ $group['title'] }}</h4>
                            <p class="text-xs text-gray-500">{{ $group['description'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Current Settings Summary -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-900">{{ __('Password Policy') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Configure password requirements for all users.') }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.update', 'password_min_length') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="password_min_length" :value="__('Minimum Password Length')" />
                        <x-text-input id="password_min_length" name="value" type="number" min="8" class="mt-1 block w-full"
                            :value="old('value', $settings['password_min_length'] ?? '12')" />
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                    </div>

                    <div>
                        <x-input-label for="require_uppercase" :value="__('Require Uppercase Letters')" />
                        <div class="flex items-center gap-4">
                            <input type="checkbox" name="value" value="1"
                                {{ old('value', $settings['require_uppercase'] ?? '1') === '1' ? 'checked' : '' }}
                                class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                        <x-primary-button>{{ __('Save Password Policy') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Current Configuration') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Setting</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Group</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Value</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($settings as $groupKey => $groupSettings)
                                @foreach ($groupSettings as $setting)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $setting->key }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $setting->group }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 truncate max-w-xs">
                                            @if ($setting->is_encrypted)
                                                <span class="text-rose-500">●●●●●●●●</span>
                                            @else
                                                {{ $setting->value ?? __('Not set') }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ $setting->input_type }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
