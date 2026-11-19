<x-app-layout title="Security Settings">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Security Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-900">{{ __('Password Policy') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Configure password requirements for all users.') }}</p>
                    </div>
                    <span class="text-xs font-semibold text-rose-500 bg-rose-50/50 px-2 py-0.5 rounded">
                        {{ __('Admin / Owner Only') }}
                    </span>
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

            <!-- Session Settings -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Session Management') }}</h3>
                </div>

                <form method="POST" action="{{ route('settings.update', 'session_timeout') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="session_timeout" :value="__('Session Timeout (minutes)')" />
                        <x-text-input id="session_timeout" name="value" type="number" min="15" class="mt-1 block w-full"
                            :value="old('value', $settings['session_timeout'] ?? '120')" />
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('Time in minutes before a user session expires.') }}</p>
                    </div>

                    <div>
                        <x-input-label for="max_login_attempts" :value="__('Max Login Attempts')" />
                        <x-text-input id="max_login_attempts" name="value" type="number" min="3" class="mt-1 block w-full"
                            :value="old('value', $settings['max_login_attempts'] ?? '5')" />
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('Maximum failed login attempts before account lockout.') }}</p>
                    </div>

                    <div>
                        <x-input-label for="email_verification_required" :value="__('Require Email Verification')" />
                        <div class="flex items-center gap-4">
                            <input type="checkbox" name="value" value="1"
                                {{ old('value', $settings['email_verification_required'] ?? '1') === '1' ? 'checked' : '' }}
                                class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('Require users to verify their email address before accessing the system.') }}</p>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                        <x-primary-button>{{ __('Save Session Settings') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Account Approval -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Account Approval') }}</h3>
                </div>

                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('Require Admin Approval for New Accounts') }}</p>
                                <p class="text-xs text-gray-500">{{ __('New user accounts must be approved by an admin before they can log in.') }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <input type="checkbox" name="require_approval" value="1"
                                    {{ old('require_approval', $settings['require_approval'] ?? '0') === '1' ? 'checked' : '' }}
                                    class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                        <x-primary-button>{{ __('Save Approval Settings') }}</x-primary-button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
