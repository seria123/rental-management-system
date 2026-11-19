<x-app-layout title="Payment Settings">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Payment Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-900">{{ __('M-Pesa Daraja Integration') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Configure M-Pesa payment gateway for rent collection.') }}</p>
                    </div>
                    <span class="text-xs font-semibold text-rose-500 bg-rose-50/50 px-2 py-0.5 rounded">
                        {{ __('Admin / Owner Only') }}
                    </span>
                </div>

                <form method="POST" action="{{ route('settings.update', 'mpesa_paybill') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="mpesa_paybill" :value="__('M-Pesa Paybill / Till Number')" />
                        <x-text-input id="mpesa_paybill" name="value" type="text" class="mt-1 block w-full"
                            :value="old('value', $settings['mpesa_paybill'] ?? '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('The Paybill or Till number for receiving M-Pesa payments.') }}</p>
                    </div>

                    <div>
                        <x-input-label for="mpesa_consumer_key" :value="__('Consumer Key')" />
                        <x-text-input id="mpesa_consumer_key" name="value" type="password" class="mt-1 block w-full"
                            :value="old('value', $settings['mpesa_consumer_key'] ?? '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('M-Pesa Daraba API consumer key. Stored encrypted.') }}</p>
                    </div>

                    <div>
                        <x-input-label for="mpesa_consumer_secret" :value="__('Consumer Secret')" />
                        <x-text-input id="mpesa_consumer_secret" name="value" type="password" class="mt-1 block w-full"
                            :value="old('value', $settings['mpesa_consumer_secret'] ?? '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('M-Pesa Daraba API consumer secret. Stored encrypted.') }}</p>
                    </div>

                    <div>
                        <x-input-label for="mpesa_passkey" :value="__('Passkey')" />
                        <x-text-input id="mpesa_passkey" name="value" type="password" class="mt-1 block w-full"
                            :value="old('value', $settings['mpesa_passkey'] ?? '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('M-Pesa Daraba API passkey. Stored encrypted.') }}</p>
                    </div>

                    <div>
                        <x-input-label for="mpesa_callback_url" :value="__('Callback URL')" />
                        <x-text-input id="mpesa_callback_url" name="value" type="url" class="mt-1 block w-full"
                            :value="old('value', $settings['mpesa_callback_url'] ?? '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('URL where M-Pesa will send payment notifications.') }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save M-Pesa Settings') }}</x-primary-button>

                        @if (session('status') === 'setting-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600"
                            >{{ __('Settings saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Bank Payment Details -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Bank Payment Details') }}</h3>
                </div>

                <form method="POST" action="{{ route('settings.update', 'bank_details') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="bank_details" :value="__('Bank Details')" />
                        <textarea id="bank_details" name="value" rows="4" class="mt-1 block w-full">{{ old('value', $settings['bank_details'] ?? '') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('value')" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('Bank account details for direct transfers.') }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Bank Details') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Payment Confirmation -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ __('Payment Confirmation Settings') }}</h3>
                </div>

                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('Auto-confirm M-Pesa Payments') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Automatically confirm payments received via M-Pesa STK push.') }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <input type="checkbox" name="auto_confirm_mpesa" value="1"
                                    {{ old('auto_confirm_mpesa', $settings['auto_confirm_mpesa'] ?? '1') === '1' ? 'checked' : '' }}
                                    class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('Send Payment Receipts') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Automatically send payment receipts to tenants.') }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <input type="checkbox" name="send_receipts" value="1"
                                    {{ old('send_receipts', $settings['send_receipts'] ?? '1') === '1' ? 'checked' : '' }}
                                    class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                        <x-primary-button>{{ __('Save Confirmation Settings') }}</x-primary-button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
