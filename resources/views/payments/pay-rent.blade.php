@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if (!$lease)
        <div class="panel p-6 rounded-2xl border border-rose-200 bg-rose-50">
            <div class="flex items-center gap-3">
                <div class="rounded-full bg-rose-100 p-2 text-rose-600">
                    @include('layouts.partials.icon', ['name' => 'alert-circle'])
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-rose-900">No Active Lease</h3>
                    <p class="text-sm text-rose-700">You do not have an active lease agreement. Rent payments cannot be processed.</p>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-12">
            <!-- Tenant & Lease Info -->
            <div class="md:col-span-4 space-y-4">
                <div class="panel p-6 rounded-2xl">
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Tenant</p>
                    <h3 class="text-lg font-semibold text-slate-900">{{ $tenant->name }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $tenant->email ?? 'No email on file' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $tenant->phone ?? 'No phone on file' }}</p>
                </div>

                <div class="panel p-6 rounded-2xl bg-indigo-50 border-indigo-100">
                    <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Lease Details</p>
                    <h3 class="text-lg font-semibold text-indigo-900">Unit {{ $lease->unit->unit_number }}</h3>
                    <p class="mt-1 text-sm text-indigo-700">{{ $lease->unit->property->name }}</p>
                    <p class="mt-2 text-sm text-indigo-600">
                        Lease: {{ $lease->start_date->format('d M Y') }} to {{ $lease->end_date->format('d M Y') }}
                    </p>
                    <p class="mt-1 text-sm text-indigo-600">
                        Monthly Rent: KSh {{ number_format($lease->monthly_rent, 2) }}
                    </p>
                    <span class="inline-flex mt-3 items-center gap-1.5 rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                        @include('layouts.partials.icon', ['name' => 'clock'])
                        Status: {{ ucfirst($lease->status) }}
                    </span>
                </div>
            </div>

            <!-- Outstanding Balance -->
            <div class="md:col-span-4">
                <div class="panel p-6 rounded-2xl bg-rose-50 border-rose-100">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-medium text-rose-600 uppercase tracking-wider">Outstanding Rent Balance</p>
                            <h2 class="text-3xl font-bold text-rose-900">KSh {{ number_format($totalOutstanding, 2) }}</h2>
                        </div>
                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                            {{ $outstandingInvoices->count() }} invoice{{ $outstandingInvoices->count() != 1 ? 's' : '' }} due
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($outstandingInvoices as $invoice)
                            <div class="flex items-center justify-between p-3 rounded-xl border border-rose-200 bg-white">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $invoice->billing_month->format('F Y') }}</p>
                                    <p class="text-xs text-slate-500">Due: {{ $invoice->due_date->format('d M Y') }}</p>
                                </div>
                                <span class="text-sm font-semibold text-rose-700">KSh {{ number_format($invoice->balance, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 text-center">No outstanding invoices.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="md:col-span-4">
                <div class="panel p-6 rounded-2xl">
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-4">Pay Rent via M-Pesa STK Push</p>

                    <form id="paymentForm" class="space-y-4">
                        @csrf

                        <!-- Step 1: Input -->
                        <div id="stepInput">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Amount to Pay (KSh)</label>
                                <input type="number" name="amount" id="amountInput" step="0.01" min="1"
                                    value="{{ old('amount', $totalOutstanding) }}"
                                    placeholder="e.g. {{ number_format($totalOutstanding, 2) }}"
                                    required
                                    class="w-full rounded-xl border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                                <p class="text-xs text-slate-500 mt-1">Maximum payable: KSh {{ number_format($totalOutstanding, 2) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">M-Pesa Phone Number</label>
                                <input type="tel" name="phone_number" id="phoneInput"
                                    value="{{ old('phone_number', $tenant->phone ?? '') }}"
                                    placeholder="07XXXXXXXX"
                                    required
                                    class="w-full rounded-xl border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                                <p class="text-xs text-slate-500 mt-1">Enter the phone number linked to your M-Pesa account (e.g. 0712345678).</p>
                                <p id="phoneError" class="text-xs text-rose-600 mt-1 hidden"></p>
                            </div>

                            @if ($outstandingInvoices->count() > 0)
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Apply to Invoice</label>
                                    <select name="rent_invoice_id" id="invoiceSelect" class="w-full rounded-xl border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Apply to oldest outstanding invoice</option>
                                        @foreach ($outstandingInvoices as $invoice)
                                            <option value="{{ $invoice->id }}">
                                                {{ $invoice->billing_month->format('F Y') }} - KSh {{ number_format($invoice->balance, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="pt-4">
                                <button type="button" id="confirmButton"
                                    class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-4 rounded-xl font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    @include('layouts.partials.icon', ['name' => 'phone'])
                                    <span id="confirmButtonText">Review Payment</span>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Confirmation -->
                        <div id="stepConfirm" class="hidden space-y-4">
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-3">Confirm Payment Details</p>
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-100">
                                        <span class="text-slate-600">Amount</span>
                                        <span id="confirmAmount" class="font-semibold text-slate-900">KSh 0.00</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-100">
                                        <span class="text-slate-600">Phone number</span>
                                        <span id="confirmPhone" class="font-semibold text-slate-900 break-all"></span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-100">
                                        <span class="text-slate-600">Lease / Unit</span>
                                        <span class="font-semibold text-slate-900">{{ $lease->unit->unit_number }} - {{ $lease->unit->property->name }}</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 mt-3">You will be prompted to enter your M-Pesa PIN on your phone to authorize this payment.</p>
                            </div>

                            <div class="pt-4">
                                <button type="button" id="payButton"
                                    class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-xl font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    @include('layouts.partials.icon', ['name' => 'phone'])
                                    <span id="payButtonText">Pay with M-Pesa</span>
                                </button>
                                <button type="button" id="editButton"
                                    class="w-full flex items-center justify-center gap-2 mt-3 border border-slate-200 bg-white px-6 py-4 rounded-xl font-semibold text-slate-700 transition hover:bg-slate-50">
                                    @include('layouts.partials.icon', ['name' => 'edit'])
                                    Edit details
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Processing -->
                        <div id="stepProcessing" class="hidden space-y-4">
                            <div class="flex flex-col items-center gap-3 py-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-600">
                                    @include('layouts.partials.icon', ['name' => 'phone'])
                                </div>
                                <div class="w-full animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-2"></div>
                                <p class="text-sm font-semibold text-slate-900" id="processingTitle">Sending STK Push...</p>
                                <p class="text-xs text-slate-500 text-center max-w-xs">Please wait while we contact Safaricom to send the PIN prompt to your phone.</p>
                            </div>

                            <div class="p-4 rounded-xl border border-green-100 bg-green-50">
                                <p class="text-sm font-semibold text-green-800 mb-2">Action required on your phone</p>
                                <p class="text-xs text-green-700">You will receive an STK Push on <strong id="processingPhone"></strong>. Enter your M-Pesa PIN to authorize the payment of <strong id="processingAmount"></strong>.</p>
                            </div>

                            <div id="processingStatus" class="mt-3 p-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 hidden">
                                <span class="text-indigo-600 font-medium">Status:</span> <span id="statusText">Awaiting payment confirmation...</span>
                            </div>
                        </div>

                        <!-- Step 4: Result -->
                        <div id="stepResult" class="hidden space-y-4">
                            <div id="resultSuccess" class="p-4 rounded-xl border border-emerald-100 bg-emerald-50 hidden">
                                <div class="flex items-start gap-3">
                                    <div class="rounded-full bg-emerald-100 p-2 text-emerald-600 shrink-0">
                                        @include('layouts.partials.icon', ['name' => 'check'])
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-emerald-900">Payment Successful</h3>
                                        <p class="text-sm text-emerald-700 mt-1">Your rent payment has been confirmed and your balance updated.</p>
                                    </div>
                                </div>
                                <div class="mt-4 space-y-2 text-sm">
                                    <div class="flex justify-between p-2 rounded-lg bg-white border border-emerald-100">
                                        <span class="text-emerald-600">M-Pesa Receipt</span>
                                        <span id="resultReceipt" class="font-semibold text-emerald-900">-</span>
                                    </div>
                                    <div class="flex justify-between p-2 rounded-lg bg-white border border-emerald-100">
                                        <span class="text-emerald-600">Amount Paid</span>
                                        <span id="resultAmountPaid" class="font-semibold text-emerald-900">-</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('tenant.payment-history') }}"
                                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm">
                                        View Payment History
                                        @include('layouts.partials.icon', ['name' => 'arrow-right'])
                                    </a>
                                </div>
                            </div>

                            <div id="resultFailure" class="p-4 rounded-xl border border-rose-100 bg-rose-50 hidden">
                                <div class="flex items-start gap-3">
                                    <div class="rounded-full bg-rose-100 p-2 text-rose-600 shrink-0">
                                        @include('layouts.partials.icon', ['name' => 'alert-circle'])
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-rose-900">Payment Not Completed</h3>
                                        <p class="text-sm text-rose-700 mt-1" id="resultFailureMessage">Your payment could not be completed at this time.</p>
                                    </div>
                                </div>
                                <div class="mt-4 space-y-2 text-sm">
                                    <p class="text-rose-600">No charge was made to your M-Pesa account and your rent balance was not affected.</p>
                                </div>
                                <div class="mt-4">
                                    <button type="button" id="retryButton"
                                        class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm">
                                        Try Again
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="mt-4">
                        <a href="{{ route('tenant.payment-history') }}"
                            class="block text-center text-sm text-indigo-600 hover:underline">
                            View Payment History & Receipts
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outstanding Invoices Detail -->
        @if ($outstandingInvoices->count() > 0)
            <div class="panel p-6 rounded-2xl mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">Outstanding Invoices</h3>
                    <a href="{{ route('tenant.payment-history') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-slate-500 uppercase text-xs border-b border-slate-200">
                                <th class="pb-3 font-semibold">Billing Month</th>
                                <th class="pb-3 font-semibold">Due Date</th>
                                <th class="pb-3 font-semibold">Amount</th>
                                <th class="pb-3 font-semibold">Paid</th>
                                <th class="pb-3 font-semibold">Balance</th>
                                <th class="pb-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($outstandingInvoices as $invoice)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 text-slate-700">{{ $invoice->billing_month->format('F Y') }}</td>
                                    <td class="py-3 text-slate-700">{{ $invoice->due_date->format('d M Y') }}</td>
                                    <td class="py-3 text-slate-700">KSh {{ number_format($invoice->amount, 2) }}</td>
                                    <td class="py-3 text-slate-700">KSh {{ number_format($invoice->amount_paid, 2) }}</td>
                                    <td class="py-3 font-semibold text-rose-700">KSh {{ number_format($invoice->balance, 2) }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold
                                            {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
</div>

@push('scripts')
<style>
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.8s linear infinite;
        margin-left: 8px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
<script>
    (function () {
        const MAX_AMOUNT = parseFloat('{{ number_format($totalOutstanding, 2) }}');
        const form = document.getElementById('paymentForm');
        const amountInput = document.getElementById('amountInput');
        const phoneInput = document.getElementById('phoneInput');
        const phoneError = document.getElementById('phoneError');
        const confirmButton = document.getElementById('confirmButton');
        const confirmButtonText = document.getElementById('confirmButtonText');

        const stepInput = document.getElementById('stepInput');
        const stepConfirm = document.getElementById('stepConfirm');
        const stepProcessing = document.getElementById('stepProcessing');
        const stepResult = document.getElementById('stepResult');

        const confirmAmount = document.getElementById('confirmAmount');
        const confirmPhone = document.getElementById('confirmPhone');

        const payButton = document.getElementById('payButton');
        const payButtonText = document.getElementById('payButtonText');
        const editButton = document.getElementById('editButton');

        const processingTitle = document.getElementById('processingTitle');
        const processingPhone = document.getElementById('processingPhone');
        const processingAmount = document.getElementById('processingAmount');
        const processingStatus = document.getElementById('processingStatus');
        const statusText = document.getElementById('statusText');

        const resultSuccess = document.getElementById('resultSuccess');
        const resultFailure = document.getElementById('resultFailure');
        const resultReceipt = document.getElementById('resultReceipt');
        const resultAmountPaid = document.getElementById('resultAmountPaid');
        const resultFailureMessage = document.getElementById('resultFailureMessage');
        const retryButton = document.getElementById('retryButton');

        let pendingPaymentId = null;
        let pendingCheckoutRequestId = null;
        let pollingInterval = null;

        // Kenyan phone validation: 07XXXXXXXX, 2547XXXXXXXX, 2541XXXXXXXX, +254...
        function normalizePhone(raw) {
            let digits = raw.replace(/\D/g, '');
            if (digits.startsWith('0')) {
                digits = '254' + digits.slice(1);
            } else if (digits.startsWith('+254')) {
                digits = digits.slice(1);
            } else if (digits.startsWith('254')) {
                // keep
            }
            return digits;
        }

        function isValidKenyanPhone(phone) {
            return /^254[17]\d{8}$/.test(phone);
        }

        function showPhoneError(msg) {
            phoneError.textContent = msg;
            phoneError.classList.remove('hidden');
            phoneInput.classList.add('border-rose-500');
        }

        function clearPhoneError() {
            phoneError.classList.add('hidden');
            phoneInput.classList.remove('border-rose-500');
        }

        function validateForm() {
            const amount = parseFloat(amountInput.value);
            const phone = normalizePhone(phoneInput.value);
            let valid = true;

            if (!amount || amount < 1) {
                valid = false;
            }
            if (amount > MAX_AMOUNT && MAX_AMOUNT > 0) {
                valid = false;
            }
            if (!isValidKenyanPhone(phone)) {
                showPhoneError('Enter a valid Kenyan number (e.g. 0712345678).');
                valid = false;
            } else {
                clearPhoneError();
            }

            confirmButton.disabled = !valid;
            return valid;
        }

        amountInput.addEventListener('input', validateForm);
        phoneInput.addEventListener('input', function () {
            clearPhoneError();
            validateForm();
        });

        // Initialize validation on load
        validateForm();

        confirmButton.addEventListener('click', function () {
            if (!validateForm()) return;

            const amount = parseFloat(amountInput.value);
            const phone = normalizePhone(phoneInput.value);

            confirmAmount.textContent = 'KSh ' + amount.toFixed(2);
            confirmPhone.textContent = phone;
            processingPhone.textContent = phone;
            processingAmount.textContent = 'KSh ' + amount.toFixed(2);

            stepInput.classList.add('hidden');
            stepConfirm.classList.remove('hidden');
        });

        editButton.addEventListener('click', function () {
            stepConfirm.classList.add('hidden');
            stepInput.classList.remove('hidden');
        });

        payButton.addEventListener('click', async function () {
            const amount = parseFloat(amountInput.value);
            const phone = normalizePhone(phoneInput.value);
            const invoiceSelect = document.getElementById('invoiceSelect');
            const invoiceId = invoiceSelect ? invoiceSelect.value : '';

            // Prevent double clicks
            payButton.disabled = true;
            payButtonText.textContent = 'Sending STK Push...';

            const formData = new FormData();
            formData.append('amount', amount);
            formData.append('phone_number', phone);
            if (invoiceId) formData.append('rent_invoice_id', invoiceId);

            try {
                const response = await fetch("{{ route('tenant.pay-rent.stk-push') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok || data.status === 'error') {
                    throw new Error(data.message || 'STK Push failed.');
                }

                pendingPaymentId = data.data.payment_id;
                pendingCheckoutRequestId = data.data.checkout_request_id;

                stepConfirm.classList.add('hidden');
                stepProcessing.classList.remove('hidden');

                // Poll for payment status every 5 seconds for up to 2 minutes
                let attempts = 0;
                const maxAttempts = 24;
                pollingInterval = setInterval(async () => {
                    attempts++;
                    try {
                        const statusResp = await fetch("{{ route('tenant.payment-status') }}?checkout_request_id=" + encodeURIComponent(pendingCheckoutRequestId), {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });
                        const statusData = await statusResp.json();

                        if (statusData.status === 'success') {
                            const localStatus = statusData.data.local_status;
                            statusText.textContent = localStatus === 'completed'
                                ? 'Payment confirmed. Updating records...'
                                : (localStatus === 'failed' ? 'Payment failed.' : 'Awaiting payment confirmation...');

                            if (localStatus === 'completed') {
                                clearInterval(pollingInterval);
                                showResult(true, statusData.data);
                                return;
                            }
                            if (localStatus === 'failed') {
                                clearInterval(pollingInterval);
                                showResult(false, statusData.data);
                                return;
                            }
                        }

                        if (attempts >= maxAttempts) {
                            clearInterval(pollingInterval);
                            statusText.textContent = 'Still waiting for confirmation. You can check your payment history shortly.';
                        }
                    } catch (e) {
                        // Polling errors are non-fatal; keep waiting for callback
                    }
                }, 5000);

            } catch (error) {
                payButton.disabled = false;
                payButtonText.textContent = 'Pay with M-Pesa';
                alert(error.message || 'Unable to initiate payment. Please try again.');
            }
        });

        function showResult(success, data) {
            stepProcessing.classList.add('hidden');
            stepResult.classList.remove('hidden');

            if (success) {
                resultSuccess.classList.remove('hidden');
                resultReceipt.textContent = data.receipt_number || 'Pending';
                resultAmountPaid.textContent = 'KSh ' + parseFloat(data.amount || 0).toFixed(2);
            } else {
                resultFailure.classList.remove('hidden');
                resultFailureMessage.textContent = data.daraja_result_desc || 'The payment was not completed. Your rent balance was not affected.';
            }
        }

        retryButton.addEventListener('click', function () {
            resultFailure.classList.add('hidden');
            resultSuccess.classList.add('hidden');
            stepResult.classList.add('hidden');
            stepInput.classList.remove('hidden');
            payButton.disabled = false;
            payButtonText.textContent = 'Pay with M-Pesa';
        });

        // Page visibility: stop polling when tab hidden to reduce load
        document.addEventListener('visibilitychange', function () {
            if (document.hidden && pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        });
    })();
</script>
@endpush