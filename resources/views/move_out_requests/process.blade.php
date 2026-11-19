@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Process Deposit Return</h2>
            <p class="text-muted mb-0">Record deposit return and damage deductions for {{ $moveOutRequest->tenant->name }}.</p>
        </div>
        <a href="{{ route('move-out-requests.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Requests
        </a>
    </div>

    <div class="row g-4">
        <!-- Request Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-slate-900">Request Details</h6>
                    <hr>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Tenant</small>
                            <h6 class="fw-semibold">{{ $moveOutRequest->tenant->name }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Unit</small>
                            <h6 class="fw-semibold">{{ $moveOutRequest->unit->unit_number ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Property</small>
                            <h6 class="fw-semibold">{{ $moveOutRequest->unit->property->name ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Move-Out Date</small>
                            <h6 class="fw-semibold">{{ $moveOutRequest->requested_move_out_date?->format('d M Y') }}</h6>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Deposit Amount</small>
                            <h6 class="fw-semibold">KSh {{ number_format($moveOutRequest->deposit_amount, 2) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deposit Processing Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold">
                        <i class="fas fa-money-bill-wave text-primary me-2"></i>
                        Deposit Allocation
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('move-out-requests.process.submit', $moveOutRequest->id) }}" method="POST">
                        @csrf

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            The return amount plus the deduction amount must equal the total deposit of <strong>KSh {{ number_format($moveOutRequest->deposit_amount, 2) }}</strong>.
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Deposit Return Amount (KSh) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="deposit_return_amount" class="form-control" required
                                       value="{{ old('deposit_return_amount', $moveOutRequest->deposit_amount) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Deposit Deduction for Damages (KSh) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="deposit_deduction_amount" class="form-control" required
                                       value="{{ old('deposit_deduction_amount', 0) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Damage Cost (KSh) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="damage_cost" class="form-control" required
                                       value="{{ old('damage_cost', 0) }}">
                                <small class="form-text text-muted">Total cost of damages found during inspection.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Damage Notes</label>
                                <textarea name="damage_notes" rows="3" class="form-control"
                                          placeholder="Describe any damages found (e.g. broken window, wall stains, unpaid bills)...">{{ old('damage_notes') }}</textarea>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-calculator me-2"></i>
                            <strong>Calculation:</strong> KSh <span id="returnAmount">{{ old('deposit_return_amount', $moveOutRequest->deposit_amount) }}</span> (return) + KSh <span id="deductionAmount">{{ old('deposit_deduction_amount', 0) }}</span> (deduction) = KSh <span id="totalAmount">0.00</span> (should equal deposit)
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('move-out-requests.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Process Move-Out
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateTotal() {
        const retInput = document.querySelector('input[name="deposit_return_amount"]');
        const dedInput = document.querySelector('input[name="deposit_deduction_amount"]');
        const ret = parseFloat(retInput.value) || 0;
        const ded = parseFloat(dedInput.value) || 0;
        document.getElementById('returnAmount').textContent = ret.toFixed(2);
        document.getElementById('deductionAmount').textContent = ded.toFixed(2);
        document.getElementById('totalAmount').textContent = (ret + ded).toFixed(2);
    }

    document.querySelectorAll('input[name="deposit_return_amount"], input[name="deposit_deduction_amount"]').forEach(input => {
        input.addEventListener('input', updateTotal);
    });

    updateTotal();
</script>
@endsection
