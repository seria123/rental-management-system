@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Request Move-Out</h2>
            <p class="text-muted mb-0">Submit your intent to vacate the unit. A 5-day notice period is required.</p>
        </div>
        <a href="{{ route('tenants.show', $tenant->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Tenant
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('tenants.move-out.store', $tenant->id) }}" method="POST">
                @csrf

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Notice Period:</strong> You must provide at least <strong>5 days</strong> notice before your intended move-out date.
                    The property owner will review your request and notify you of approval.
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Current Unit</label>
                        <h6 class="fw-semibold text-slate-900">
                            {{ $tenant->unit->unit_number ?? 'N/A' }}
                            @if($tenant->unit->property)
                                - {{ $tenant->unit->property->name }}
                            @endif
                        </h6>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Deposit on File</label>
                        <h6 class="fw-semibold text-slate-900">
                            KSh {{ number_format($tenant->leases()->where('status', 'active')->first()->deposit_amount ?? 0, 2) }}
                        </h6>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Intended Move-Out Date <span class="text-danger">*</span></label>
                        <input type="date" name="requested_move_out_date" class="form-control @error('requested_move_out_date') is-invalid @enderror"
                               min="{{ now()->addDays(5)->format('Y-m-d') }}" required>

                        @error('requested_move_out_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Must be at least 5 days from today.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Reason for Moving Out</label>
                        <textarea name="reason" rows="3" class="form-control" placeholder="e.g. Relocating for work, buying a home, etc."></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('tenants.show', $tenant->id) }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane me-2"></i> Submit Move-Out Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
