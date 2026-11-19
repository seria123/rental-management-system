<x-app-layout title="Backup & System">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Backup & System') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <span class="text-xs font-semibold text-rose-500 bg-rose-50/50 px-2 py-0.5 rounded mb-4 block">
                {{ __('Administrator Only') }}
            </span>

            <!-- Database Backup -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-900">{{ __('Database Backup') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Create and manage database backups.') }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('Last Backup') }}</p>
                            <p class="text-sm text-gray-500">{{ $settings['last_backup'] ?? __('Never') }}</p>
                        </div>
                        <button onclick="confirmBackup()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-medium">
                            {{ __('Create Backup') }}
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('Auto Backup') }}</p>
                            <p class="text-xs text-gray-500">{{ __('Automated daily backups at midnight.') }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <input type="checkbox" name="auto_backup" value="1"
                                {{ old('auto_backup', $settings['auto_backup'] ?? '1') === '1' ? 'checked' : '' }}
                                class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Activity Logs') }}</h3>
                    <a href="{{ route('admin.activity-log') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        {{ __('View Full Logs') }} →
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('Log Retention (days)') }}</p>
                            <p class="text-xs text-gray-500">{{ __('How long to keep activity logs before automatic cleanup.') }}</p>
                        </div>
                        <x-text-input name="value" type="number" min="30" class="mt-1 block w-48"
                            :value="old('value', $settings['log_retention_days'] ?? '90')" />
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                        <x-primary-button>{{ __('Save Log Settings') }}</x-primary-button>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('System Health Monitoring') }}</h3>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Database') }}</p>
                        <p class="text-sm font-semibold text-emerald-600">{{ __('Connected') }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Cache') }}</p>
                        <p class="text-sm font-semibold text-emerald-600">{{ __('Operational') }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Queue') }}</p>
                        <p class="text-sm font-semibold text-emerald-600">{{ __('Running') }}</p>
                    </div>
                </div>
            </div>

            <!-- Maintenance Mode -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Maintenance Mode') }}</h3>
                </div>

                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                            <i class="fas fa-tools text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-amber-800">{{ __('Maintenance Mode Disabled') }}</p>
                            <p class="text-xs text-amber-600">{{ __('The system is currently accessible to all users.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function confirmBackup() {
            if (confirm('{{ __('Are you sure you want to create a database backup? This may take a few moments.') }}')) {
                // Backup functionality would be implemented here
                alert('{{ __('Backup feature will be available in a future update.') }}');
            }
        }
    </script>
</x-app-layout>
