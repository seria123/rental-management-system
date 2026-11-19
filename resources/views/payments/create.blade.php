@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Collections</p>
                <h1 class="page-title">Record payment</h1>
            </div>
            <a href="{{ route('payments.index') }}" class="ghost-button">Back</a>
        </div>

        <div class="panel p-6">
            <form action="{{ route('payments.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="tenant_id" class="form-label">Tenant</label>
                        <select name="tenant_id" id="tenant_id" class="form-select" required>
                            <option value="">Select tenant</option>
                            @foreach(App\Models\Tenant::with(['currentLease.unit.property'])->get() as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->name }} - {{ $tenant->currentLease?->unit?->property?->name ?? 'No property' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lease_id" class="form-label">Lease</label>
                        <select name="lease_id" id="lease_id" class="form-select">
                            <option value="">Select lease (optional)</option>
                            @foreach(App\Models\Lease::with(['tenant', 'unit.property'])->get() as $lease)
                                <option value="{{ $lease->id }}">{{ $lease->tenant?->name }} - {{ $lease->unit?->unit_number ?? 'Unit' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" required>
                    </div>

                    <div>
                        <label for="payment_date" class="form-label">Payment date</label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div>
                        <label for="payment_method" class="form-label">Payment method</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Mpesa">Mpesa</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <div>
                        <label for="phone_number" class="form-label">M-Pesa phone number</label>
                        <input type="tel" name="phone_number" id="phone_number" class="form-control" placeholder="e.g. 0712345678"
                            value="{{ old('phone_number') }}">
                    </div>

                    <div>
                        <label for="payment_type" class="form-label">Payment type</label>
                        <select name="payment_type" id="payment_type" class="form-select" required>
                            <option value="rent">Rent</option>
                            <option value="deposit">Deposit</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="transaction_reference" class="form-label">Reference</label>
                        <input type="text" name="transaction_reference" id="transaction_reference" class="form-control">
                    </div>

                    <div>
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('payments.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" id="savePaymentBtn"
                        class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 text-sm font-semibold rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        @include('layouts.partials.icon', ['name' => 'phone'])
                        <span id="savePaymentBtnText">Pay Rent</span>
                    </button>
                </div>

                <!-- M-Pesa STK Push status message (shown during processing) -->
                <div id="mpesaStatus" class="hidden p-4 rounded-xl border border-green-100 bg-green-50 mt-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-green-600 shrink-0">
                            @include('layouts.partials.icon', ['name' => 'phone'])
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Sending STK Push...</p>
                            <p class="text-xs text-green-700 mt-1">Please check your phone and enter your M-Pesa PIN to authorize the payment. The transaction will be confirmed once Safaricom responds.</p>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-green-600 animate-pulse">Awaiting payment confirmation...</div>
                </div>
            </form>
        </div>
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
        margin-left: 6px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
<script>
    (function () {
        const form = document.getElementById('paymentForm');
        const btn = document.getElementById('savePaymentBtn');
        const btnText = document.getElementById('savePaymentBtnText');
        const methodSelect = document.getElementById('payment_method');
        const mpesaStatus = document.getElementById('mpesaStatus');

        if (!form || !btn) return;

        // Capture the original button HTML (includes the phone icon) so we can restore it.
        const originalBtnHtml = btn.innerHTML;
        const originalBtnText = btnText ? btnText.innerText : 'Pay Rent';

        function setLoading(loading) {
            if (loading) {
                btn.disabled = true;
                if (btnText) btnText.innerText = 'Sending...';
                btn.innerHTML = originalBtnHtml + ' <span class="loading-spinner" style="width:12px;height:12px;border-width:2px;"></span>';
                if (methodSelect && methodSelect.value === 'Mpesa') {
                    mpesaStatus.classList.remove('hidden');
                }
            } else {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
                if (btnText) btnText.innerText = originalBtnText;
            }
        }

        form.addEventListener('submit', function () {
            setLoading(true);
        });

        // Reveal M-Pesa hint when Mpesa method is selected
        methodSelect?.addEventListener('change', function () {
            if (methodSelect.value === 'Mpesa') {
                btn.classList.add('bg-green-600', 'hover:bg-green-700');
                btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            } else {
                mpesaStatus.classList.add('hidden');
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            }
        });
    })();
</script>
@endpush
@endsection
