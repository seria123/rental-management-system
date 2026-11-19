<x-app-layout title="Profile">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Profile Header -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    <div class="flex-shrink-0">
                        @if ($profile && $profile->profile_picture_path && Storage::disk('public')->exists($profile->profile_picture_path))
                            <img src="{{ Storage::disk('public')->url($profile->profile_picture_path) }}" alt="Profile" class="h-24 w-24 rounded-full object-cover border-4 border-indigo-100">
                        @else
                            <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        @if ($profile && $profile->profile_picture_path)
                            <form action="{{ route('profile.picture.download') }}" method="GET" class="mt-2">
                                <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 underline">
                                    Download Photo
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        @if ($profile && $profile->username)
                            <p class="text-sm text-gray-500 mt-1">@{{ $profile->username }}</p>
                        @endif
                        <p class="text-sm text-gray-600 mt-2">{{ ucfirst(str_replace('_', ' ', $user->role ?? 'admin')) }}</p>

                        <div class="mt-4 flex gap-3">
                            <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Edit Profile
                            </a>
                            @if ($profile && $profile->profile_picture_path)
                                <form action="{{ route('profile.picture.delete') }}" method="POST" onsubmit="return confirm('Remove profile picture?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50">
                                        Remove Photo
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Edit Profile Details') }}</h3>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        {{ __('Open full editor') }} →
                    </a>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $user->name)" required autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full"
                                :value="old('username', $profile?->username)" autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('username')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full"
                                :value="old('phone', $profile?->phone)" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Role-Specific Information -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ $roleSpecificData['label'] }}</h3>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($roleSpecificData['fields'] as $label => $value)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ $label }}</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Account Information -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Account Information') }}</h3>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('User ID') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->id }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Role') }}</p>
                        <p class="text-sm font-semibold text-gray-900 capitalize">{{ ucfirst(str_replace('_', ' ', $user->role ?? 'admin')) }}</p>
                    </div>
<div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Account Status') }}</p>
                            <p class="text-sm font-semibold text-{{ $user->status === 'active' ? 'emerald' : 'rose' }}-600 capitalize">
                                {{ __($user->status ?? 'active') }}
                            </p>
                        </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Registered') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->created_at?->format('d M Y') }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Last Login') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->last_login ?? __('Never') }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Email Verification') }}</p>
                        <p class="text-sm font-semibold text-{{ $user->hasVerifiedEmail() ? 'emerald' : 'rose' }}-600">
                            {{ $user->hasVerifiedEmail() ? __('Verified') : __('Unverified') }}
                        </p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Email Address') }}</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Phone') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $profile?->phone ?? __('Not set') }}</p>
                    </div>
                </div>
            </div>

            <!-- Personal Information (Editable) -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Personal Information') }}</h3>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        {{ __('Edit Details') }} →
                    </a>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Full Name') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Gender') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $profile?->gender ? ucfirst($profile?->gender) : __('Not set') }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Date of Birth') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $profile?->date_of_birth?->format('d M Y') ?? __('Not set') }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('National ID') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $profile?->national_id ?? __('Not set') }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Physical Address') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $profile?->physical_address ?? __('Not set') }}</p>
                    </div>
                    @if ($profile && $profile->emergency_contact_name)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Emergency Contact') }}</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $profile->emergency_contact_name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Security Status -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Security') }}</h3>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Two-Factor Auth') }}</p>
                        <div class="flex items-center gap-3">
                            <span class="status-badge {{ $twoFactorSettings && $twoFactorSettings->is_enabled ? 'active' : 'pending' }}">
                                {{ $twoFactorSettings && $twoFactorSettings->is_enabled ? __('Enabled') : __('Disabled') }}
                            </span>
                            <a href="{{ route('profile.two-factor') }}" method="POST" class="text-xs text-indigo-600 hover:text-indigo-800">
                                {{ $twoFactorSettings && $twoFactorSettings->is_enabled ? __('Manage') : __('Setup') }}
                            </a>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Active Sessions') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->sessions->count() }}</p>
                        <a href="{{ route('settings.sessions') }}" class="text-xs text-indigo-600 hover:text-indigo-800 mt-1 block">
                            {{ __('Manage Sessions') }} →
                        </a>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ __('Password Last Changed') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->updated_at?->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Notification Preferences Summary -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Notification Preferences') }}</h3>
                    <a href="{{ route('settings.notifications') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        {{ __('Manage All') }} →
                    </a>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        __('Email Notifications') => $notificationPreferences->email_notifications ?? true,
                        __('SMS Notifications') => $notificationPreferences->sms_notifications ?? false,
                        __('In-App Notifications') => $notificationPreferences->in_app_notifications ?? true,
                        __('Rent Payment Alerts') => $notificationPreferences->rent_payment_notifications ?? true,
                        __('Maintenance Alerts') => $notificationPreferences->maintenance_notifications ?? true,
                        __('System Announcements') => $notificationPreferences->system_announcements ?? true,
                    ] as $label => $enabled)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                            <span class="text-xs font-semibold {{ $enabled ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $enabled ? __('On') : __('Off') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
