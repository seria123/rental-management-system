<x-app-layout title="Notification Settings">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Notification Preferences') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-900">{{ __('Notification Preferences') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Choose how you want to receive notifications.') }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.notifications.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="space-y-6">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('Email Notifications') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Receive notifications via email.') }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" name="email_notifications"
                                        {{ $notificationPreferences->email_notifications ? 'checked' : '' }}
                                        class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('SMS Notifications') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Receive notifications via SMS.') }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" name="sms_notifications"
                                        {{ $notificationPreferences->sms_notifications ? 'checked' : '' }}
                                        class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('In-App Notifications') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Receive notifications within the application.') }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" name="in_app_notifications"
                                        {{ $notificationPreferences->in_app_notifications ? 'checked' : '' }}
                                        class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('Rent Payment Notifications') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Get notified when rent payments are received or overdue.') }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" name="rent_payment_notifications"
                                        {{ $notificationPreferences->rent_payment_notifications ? 'checked' : '' }}
                                        class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('Maintenance Notifications') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Get notified about maintenance requests and updates.') }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" name="maintenance_notifications"
                                        {{ $notificationPreferences->maintenance_notifications ? 'checked' : '' }}
                                        class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('System Announcements') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Receive system-wide announcements and updates.') }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" name="system_announcements"
                                        {{ $notificationPreferences->system_announcements ? 'checked' : '' }}
                                        class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-6 border-t border-slate-200">
                        <x-primary-button>{{ __('Save Preferences') }}</x-primary-button>

                        @if (session('status') === 'notifications-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600"
                            >{{ __('Preferences saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
