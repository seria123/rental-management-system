@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Register New Tenant</h2>
            <p class="text-muted mb-0">Add tenant personal details and assign a rental unit.</p>
        </div>

        <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Tenants
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <form action="{{ route('tenants.store') }}" method="POST">
                @csrf

                {{-- PERSONAL INFORMATION --}}
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-user text-primary me-2"></i>
                    Personal Information
                </h5>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name *</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Enter full name" required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">National ID Number *</label>
                        <input type="text" name="id_number"
                               class="form-control @error('id_number') is-invalid @enderror"
                               value="{{ old('id_number') }}"
                               placeholder="Enter ID number" required>

                        @error('id_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone Number *</label>
                        <input type="tel" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}"
                               placeholder="e.g. 0712345678" required>

                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gender *</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date of Birth *</label>
                        <input type="date" name="date_of_birth"
                               class="form-control"
                               value="{{ old('date_of_birth') }}"
                               max="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Occupation *</label>
                        <input type="text" name="occupation"
                               class="form-control"
                               value="{{ old('occupation') }}"
                               placeholder="e.g. Teacher, Developer" required>
                    </div>

                </div>

                <hr class="my-4">

                {{-- UNIT ALLOCATION --}}
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-building text-primary me-2"></i>
                    Rental Information
                </h5>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Select Unit *</label>

                        <select name="unit_id" class="form-select" required>
                            <option value="">Choose Unit</option>

                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->unit_number ?? $unit->unit_no ?? $unit->name }}
                                    @if(isset($unit->property))
                                        - {{ $unit->property->name }}
                                    @endif
                                </option>
                            @endforeach

                        </select>

                        @error('unit_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Starting Month *</label>

                        <input type="month" name="month"
                               class="form-control"
                               value="{{ old('month', date('Y-m')) }}"
                               required>
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('tenants.index') }}"
                       class="btn btn-light px-4">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>
                        Register Tenant
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection