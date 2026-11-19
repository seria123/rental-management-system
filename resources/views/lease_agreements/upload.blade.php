@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Upload Lease Agreement</h2>
            <p class="text-muted mb-0">Upload the signed lease agreement document for <strong>{{ $lease->tenant->name }}</strong>.</p>
        </div>
        <a href="{{ route('tenants.show', $lease->tenant_id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Tenant
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('leases.agreements.store', $lease->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold">Lease Agreement Document <span class="text-danger">*</span></label>
                    <input type="file" name="agreement_file" class="form-control @error('agreement_file') is-invalid @enderror"
                           accept=".pdf,.doc,.docx" required>
                    <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX. Maximum size: 10MB.</small>

                    @error('agreement_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Once uploaded, both the property owner/agent and the tenant can read and sign the document digitally.
                    Each party will receive a copy after both signatures are complete.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('tenants.show', $lease->tenant_id) }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-upload me-2"></i> Upload Agreement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
