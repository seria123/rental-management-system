<x-app-layout title="Edit Profile">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Profile Picture Upload -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Profile Picture') }}</h3>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="flex flex-col md:flex-row gap-6 items-start md:items-center">
                        <div class="flex-shrink-0">
                            @if ($user->profile && $user->profile->profile_picture_path && Storage::disk('public')->exists($user->profile->profile_picture_path))
                                <img src="{{ Storage::disk('public')->url($user->profile->profile_picture_path) }}" alt="Current" class="h-32 w-32 rounded-full object-cover border-4 border-indigo-100 mb-4">
                            @else
                                <div class="h-32 w-32 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-xl mb-4">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                            <label class="block">
                                <input type="file" name="profile_picture" accept="image/*" class="sr-only" id="profile_picture">
                                <button type="button" onclick="document.getElementById('profile_picture').click()"
                                    class="px-6 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    Change Photo
                                </button>
                            </label>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm text-gray-600">
                                Upload a profile picture. Recommended size: 200x200px. Max file size: 2MB.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <x-primary-button>{{ __('Save Profile') }}</x-primary-button>

                        @if (session('status') === 'profile-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600"
                            >{{ __('Saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Personal Information Form -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Personal Information') }}</h3>
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
                                :value="old('username', $user->profile?->username)" autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('username')" />
                            <p class="text-xs text-gray-500 mt-1">{{ __('May only contain letters, numbers, dashes, and underscores.') }}</p>
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
                                :value="old('phone', $user->profile?->phone)" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <div>
                            <x-input-label for="gender" :value="__('Gender')" />
                            <select id="gender" name="gender" class="mt-1 block w-full">
                                <option value="">{{ __('Select gender') }}</option>
                                <option value="male" {{ old('gender', $user->profile?->gender) === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                <option value="female" {{ old('gender', $user->profile?->gender) === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                <option value="other" {{ old('gender', $user->profile?->gender) === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                <option value="prefer_not_to_say" {{ old('gender', $user->profile?->gender) === 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('Prefer not to say') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                        </div>

                        <div>
                            <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                            <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full"
                                :value="old('date_of_birth', $user->profile?->date_of_birth ? \Carbon\Carbon::parse($user->profile->date_of_birth)->format('Y-m-d') : '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
                        </div>

                        <div>
                            <x-input-label for="national_id" :value="__('National ID')" />
                            <x-text-input id="national_id" name="national_id" type="text" class="mt-1 block w-full"
                                :value="old('national_id', $user->profile?->national_id)" />
                            <x-input-error class="mt-2" :messages="$errors->get('national_id')" />
                        </div>

                        <div class="lg:col-span-2">
                            <x-input-label for="physical_address" :value="__('Physical Address')" />
                            <x-text-input id="physical_address" name="physical_address" type="text" class="mt-1 block w-full"
                                :value="old('physical_address', $user->profile?->physical_address)" />
                            <x-input-error class="mt-2" :messages="$errors->get('physical_address')" />
                        </div>

                        <!-- Role-specific fields -->
                        @if ($user->canAccessPaymentSettings() || $user->isRole('owner'))
                            <div>
                                <x-input-label for="business_name" :value="__('Business Name')" />
                                <x-text-input id="business_name" name="business_name" type="text" class="mt-1 block w-full"
                                    :value="old('business_name', $user->profile?->business_name)" />
                                <x-input-error class="mt-2" :messages="$errors->get('business_name')" />
                            </div>
                            <div>
                                <x-input-label for="kra_pin" :value="__('KRA PIN')" />
                                <x-text-input id="kra_pin" name="kra_pin" type="text" class="mt-1 block w-full"
                                    :value="old('kra_pin', $user->profile?->kra_pin)" />
                                <x-input-error class="mt-2" :messages="$errors->get('kra_pin')" />
                            </div>
                        @endif

                        @if ($user->isRole('agent'))
                            <div>
                                <x-input-label for="agency_name" :value="__('Agency Name')" />
                                <x-text-input id="agency_name" name="agency_name" type="text" class="mt-1 block w-full"
                                    :value="old('agency_name', $user->profile?->agency_name)" />
                                <x-input-error class="mt-2" :messages="$errors->get('agency_name')" />
                            </div>
                            <div>
                                <x-input-label for="assigned_property" :value="__('Assigned Properties')" />
                                <x-text-input id="assigned_property" name="assigned_property" type="text" class="mt-1 block w-full"
                                    :value="old('assigned_property', $user->profile?->assigned_property)" />
                                <x-input-error class="mt-2" :messages="$errors->get('assigned_property')" />
                            </div>
                        @endif

                        @if ($user->isRole('caretaker'))
                            <div>
                                <x-input-label for="assigned_property" :value="__('Assigned Property')" />
                                <x-text-input id="assigned_property" name="assigned_property" type="text" class="mt-1 block w-full"
                                    :value="old('assigned_property', $user->profile?->assigned_property)" />
                                <x-input-error class="mt-2" :messages="$errors->get('assigned_property')" />
                            </div>
                            <div>
                                <x-input-label for="emergency_contact_name" :value="__('Emergency Contact Name')" />
                                <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full"
                                    :value="old('emergency_contact_name', $user->profile?->emergency_contact_name)" />
                                <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_name')" />
                            </div>
                            <div>
                                <x-input-label for="emergency_contact_phone" :value="__('Emergency Contact Phone')" />
                                <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="tel" class="mt-1 block w-full"
                                    :value="old('emergency_contact_phone', $user->profile?->emergency_contact_phone)" />
                                <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_phone')" />
                            </div>
                        @endif

                        @if ($user->isRole('tenant'))
                            <div>
                                <x-input-label for="national_id" :value="__('National ID')" />
                                <x-text-input id="national_id" name="national_id" type="text" class="mt-1 block w-full"
                                    :value="old('national_id', $user->profile?->national_id)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('national_id')" />
                            </div>
                            <div>
                                <x-input-label for="emergency_contact_name" :value="__('Emergency Contact Name')" />
                                <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full"
                                    :value="old('emergency_contact_name', $user->profile?->emergency_contact_name)" />
                                <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_name')" />
                            </div>
                            <div>
                                <x-input-label for="emergency_contact_phone" :value="__('Emergency Contact Phone')" />
                                <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="tel" class="mt-1 block w-full"
                                    :value="old('emergency_contact_phone', $user->profile?->emergency_contact_phone)" />
                                <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_phone')" />
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>

                        @if (session('status') === 'profile-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600"
                            >{{ __('Saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Password Change Section -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Change Password') }}</h3>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        {{ __('Back to Profile') }} →
                    </a>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="mt-6 space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="current_password" :value="__('Current Password')" />
                        <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('New Password')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">
                            {{ __('Must be at least 8 characters and include uppercase, lowercase, numbers, and symbols.') }}
                        </p>
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Password') }}</x-primary-button>

                        @if (session('status') === 'password-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600"
                            >{{ __('Password changed successfully.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Two-Factor Authentication -->
            @php
                $twoFactorEnabled = $user->twoFactorSettings && $user->twoFactorSettings->is_enabled;
            @endphp
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Two-Factor Authentication') }}</h3>
                    <a href="{{ route('profile.two-factor') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        {{ $twoFactorEnabled ? __('Disable 2FA') : __('Enable 2FA') }} →
                    </a>
                </div>

                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="flex-shrink-0">
                        <input type="checkbox" wire:model="twoFactorEnabled" class="rounded text-indigo-600 focus:ring-indigo-500">
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('Enable two-factor authentication for extra security') }}</p>
                        <p class="text-sm text-gray-500">{{ __('Requires a verification code from your authenticator app at login.') }}</p>
                    </div>
                    <span class="text-xs font-semibold {{ $twoFactorEnabled ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ $twoFactorEnabled ? __('Active') : __('Inactive') }}
                    </span>
                </div>
            </div>

            <!-- Delete Account Section -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg border border-rose-100">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>
