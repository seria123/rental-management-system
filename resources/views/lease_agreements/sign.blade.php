@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Lease Agreement</h2>
            <p class="text-muted mb-0">Review and sign the lease agreement for {{ $agreement->lease->tenant->name }}.</p>
        </div>
        <a href="{{ route('tenants.show', $agreement->lease->tenant_id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Tenant
        </a>
    </div>

    <div class="row g-4">
        <!-- Agreement Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold">
                        <i class="fas fa-file-contract text-primary me-2"></i>
                        Agreement Details
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted">Document</small>
                            <h6 class="fw-semibold">{{ $agreement->original_filename }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Unit</small>
                            <h6 class="fw-semibold">{{ $agreement->lease->unit->unit_number ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Property</small>
                            <h6 class="fw-semibold">{{ $agreement->lease->unit->property->name ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Tenant</small>
                            <h6 class="fw-semibold">{{ $agreement->lease->tenant->name }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Monthly Rent</small>
                            <h6 class="fw-semibold">KSh {{ number_format($agreement->lease->monthly_rent, 2) }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Deposit</small>
                            <h6 class="fw-semibold">KSh {{ number_format($agreement->lease->deposit_amount, 2) }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Lease Start</small>
                            <h6 class="fw-semibold">{{ $agreement->lease->start_date?->format('d M Y') ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Lease End</small>
                            <h6 class="fw-semibold">{{ $agreement->lease->end_date?->format('d M Y') ?? 'N/A' }}</h6>
                        </div>
                    </div>

                    <hr>

                    <!-- Signature Status -->
                    <div class="d-grid gap-2">
                        <div class="d-flex align-items-center p-3 rounded border {{ $agreement->owner_signed ? 'bg-success bg-opacity-10 border-success' : 'bg-light border-secondary' }}">
                            <span class="badge {{ $agreement->owner_signed ? 'bg-success' : 'bg-secondary' }} me-3">
                                {{ $agreement->owner_signed ? 'Signed' : 'Pending' }}
                            </span>
                            <div>
                                <small class="text-muted d-block">Owner / Agent</small>
                                <strong>{{ $agreement->owner_signed_at?->format('d M Y H:i') ?? 'Not yet signed' }}</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center p-3 rounded border {{ $agreement->tenant_signed ? 'bg-success bg-opacity-10 border-success' : 'bg-light border-secondary' }}">
                            <span class="badge {{ $agreement->tenant_signed ? 'bg-success' : 'bg-secondary' }} me-3">
                                {{ $agreement->tenant_signed ? 'Signed' : 'Pending' }}
                            </span>
                            <div>
                                <small class="text-muted d-block">Tenant</small>
                                <strong>{{ $agreement->tenant_signed_at?->format('d M Y H:i') ?? 'Not yet signed' }}</strong>
                            </div>
                        </div>
                    </div>

                    @if ($agreement->isFullySigned())
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Fully signed by both parties!</strong> The agreement is complete.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold">
                        <i class="fas fa-pen-nib text-primary me-2"></i>
                        Sign Document
                    </h5>
                </div>
                <div class="card-body p-4">

                    @if (! $agreement->owner_signed && Auth::user()->hasAnyRole(['admin', 'property_manager']))
                        <form action="{{ route('lease-agreements.sign', $agreement->id) }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="sign_as" value="owner">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-signature me-2"></i> Sign as Owner / Agent
                            </button>
                        </form>
                    @elseif ($agreement->owner_signed && Auth::user()->hasAnyRole(['admin', 'property_manager']))
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i> You have already signed as owner.
                        </div>
                    @endif

                    @if (! $agreement->tenant_signed && Auth::user()->hasAnyRole(['tenant']))
                        <form action="{{ route('lease-agreements.sign', $agreement->id) }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="sign_as" value="tenant">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-signature me-2"></i> Sign as Tenant
                            </button>
                        </form>
                    @elseif ($agreement->tenant_signed && Auth::user()->hasAnyRole(['tenant']))
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i> You have already signed as tenant.
                        </div>
                    @endif

                    @if (Auth::user()->hasAnyRole(['admin', 'property_manager']) && ! $agreement->owner_signed)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            As the property owner/agent, you need to sign first before the tenant can sign.
                        </div>
                    @endif

                    <hr>

                    <!-- Download -->
                    @if ($agreement->file_path)
                        <a href="{{ route('lease-agreements.download', $agreement->id) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-download me-2"></i> Download Agreement Document
                        </a>
                    @else
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            No agreement document has been uploaded yet. Please ask the property owner to upload it.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
