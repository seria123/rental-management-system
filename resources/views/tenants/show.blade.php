@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tenant Profile</h2>
            <p class="text-muted mb-0">View tenant personal and rental information.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>

            <a href="{{ route('tenants.edit', $tenant->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Edit Tenant
            </a>

            @if ($tenant->leases()->exists())
                @php $activeLease = $tenant->leases->where('status', 'active')->first(); @endphp
                @if ($activeLease)
                    @php $agreement = \App\Models\LeaseAgreement::where('lease_id', $activeLease->id)->first(); @endphp
                    @if ($agreement)
                        <a href="{{ route('leases.agreements.show', $activeLease->id) }}" class="btn btn-info">
                            <i class="fas fa-file-contract me-2"></i> Lease Agreement
                        </a>
                    @else
                        <a href="{{ route('leases.agreements.store', $activeLease->id) }}" class="btn btn-warning">
                            <i class="fas fa-upload me-2"></i> Upload Agreement
                        </a>
                    @endif

                    @php $moveOutRequest = \App\Models\MoveOutRequest::where('tenant_id', $tenant->id)->where('status', 'pending')->first(); @endphp
                    @if ($moveOutRequest)
                        <button class="btn btn-outline-warning disabled" title="Move-out request pending">
                            <i class="fas fa-clock me-2"></i> Move-Out Pending
                        </button>
                    @else
                        <a href="{{ route('tenants.move-out.create', $tenant->id) }}" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Request Move-Out
                        </a>
                    @endif
                @endif
            @endif
        </div>
    </div>

    <div class="row g-4">

        {{-- TENANT PROFILE CARD --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center p-4">

                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 90px; height: 90px; font-size: 32px;">
                        <i class="fas fa-user"></i>
                    </div>

                    <h4 class="fw-bold mb-1">{{ $tenant->name }}</h4>

                    <p class="text-muted mb-3">
                        Tenant ID: #{{ $tenant->id }}
                    </p>

                    <span class="badge rounded-pill px-3 py-2
                        {{ $tenant->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($tenant->status ?? 'Active') }}
                    </span>

                    <hr>

                    <div class="text-start">

                        <p class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            {{ $tenant->phone ?? 'Not provided' }}
                        </p>

                        <p class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            {{ $tenant->email ?? 'Not provided' }}
                        </p>

                        <p class="mb-0">
                            <i class="fas fa-id-card text-primary me-2"></i>
                            {{ $tenant->id_number ?? $tenant->national_id ?? 'Not provided' }}
                        </p>

                    </div>
                </div>
            </div>
        </div>

        {{-- TENANT DETAILS --}}
        <div class="col-lg-8">

            {{-- PERSONAL INFORMATION --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold">
                        <i class="fas fa-user-circle text-primary me-2"></i>
                        Personal Information
                    </h5>
                </div>

                <div class="card-body px-4">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <small class="text-muted">Full Name</small>
                            <h6 class="fw-semibold">{{ $tenant->name }}</h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">National ID Number</small>
                            <h6 class="fw-semibold">
                                {{ $tenant->id_number ?? $tenant->national_id ?? 'N/A' }}
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Phone Number</small>
                            <h6 class="fw-semibold">{{ $tenant->phone ?? 'N/A' }}</h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Email Address</small>
                            <h6 class="fw-semibold">{{ $tenant->email ?? 'N/A' }}</h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Gender</small>
                            <h6 class="fw-semibold">{{ $tenant->gender ?? 'N/A' }}</h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Date of Birth</small>
                            <h6 class="fw-semibold">
                                {{ $tenant->date_of_birth?->format('d M Y') ?? 'N/A' }}
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Occupation</small>
                            <h6 class="fw-semibold">{{ $tenant->occupation ?? 'N/A' }}</h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Address</small>
                            <h6 class="fw-semibold">{{ $tenant->address ?? 'N/A' }}</h6>
                        </div>

                    </div>
                </div>
            </div>

            {{-- RENTAL INFORMATION --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold">
                        <i class="fas fa-building text-primary me-2"></i>
                        Rental Information
                    </h5>
                </div>

                <div class="card-body px-4">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <small class="text-muted">Unit Number</small>
                            <h6 class="fw-semibold">
                                {{ $tenant->unit?->unit_number ?? $tenant->unit?->unit_no ?? 'Not assigned' }}
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Property</small>
                            <h6 class="fw-semibold">
                                {{ $tenant->unit?->property?->name ?? 'N/A' }}
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Monthly Rent</small>
                            <h6 class="fw-semibold">
                                {{ $tenant->unit?->monthly_rent ? 'KSh ' . number_format($tenant->unit->monthly_rent, 2) : 'N/A' }}
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Starting Month</small>
                            <h6 class="fw-semibold">
                                {{ $tenant->month ?? 'N/A' }}
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Registration Date</small>
                            <h6 class="fw-semibold">
                                {{ $tenant->created_at?->format('d M Y') }}
                            </h6>
                        </div>

                    </div>
                </div>
            </div>

            {{-- EMERGENCY CONTACT --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold">
                        <i class="fas fa-phone-alt text-danger me-2"></i>
                        Emergency Contact
                    </h5>
                </div>

                <div class="card-body px-4">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <small class="text-muted">Contact Name</small>
                            <h6 class="fw-semibold">
                                {{ $tenant->emergency_contact_name ?? 'Not provided' }}
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Contact Phone</small>
                            <h6 class="fw-semibold">
                                {{ $tenant->emergency_contact_phone ?? 'Not provided' }}
                            </h6>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- DELETE TENANT --}}
    <div class="mt-4 text-end">
        <form action="{{ route('tenants.destroy', $tenant->id) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to delete this tenant?')">

            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-outline-danger">
                <i class="fas fa-trash me-2"></i> Delete Tenant
            </button>
        </form>
    </div>

</div>

@endsection