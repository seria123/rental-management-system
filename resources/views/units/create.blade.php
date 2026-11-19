@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Inventory</p>
                <h1 class="page-title">Add unit</h1>
            </div>
            <a href="{{ route('units.index') }}" class="ghost-button">Back</a>
        </div>

        <div class="panel p-6">
            <form action="{{ route('units.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="property_id" class="form-label">Property</label>
                        <select name="property_id" id="property_id" class="form-select" required>
                            <option value="">Select property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="unit_number" class="form-label">Unit number</label>
                        <input type="text" name="unit_number" id="unit_number" class="form-control" required>
                    </div>

                    <div>
                        <label for="unit_type" class="form-label">Unit type</label>
                        <select name="unit_type" id="unit_type" class="form-select" required>
                            <option value="">Select type</option>
                            <option value="studio">Studio</option>
                            <option value="one_bedroom">1 Bedroom</option>
                            <option value="two_bedroom">2 Bedroom</option>
                            <option value="three_bedroom">3 Bedroom</option>
                            <option value="commercial">Commercial</option>
                        </select>
                    </div>

                    <div>
                        <label for="floor" class="form-label">Floor</label>
                        <input type="text" name="floor" id="floor" class="form-control">
                    </div>

                    <div>
                        <label for="monthly_rent" class="form-label">Monthly rent</label>
                        <input type="number" step="0.01" min="0" name="monthly_rent" id="monthly_rent" class="form-control" required>
                    </div>

                    <div>
                        <label for="deposit_amount" class="form-label">Deposit amount</label>
                        <input type="number" step="0.01" min="0" name="deposit_amount" id="deposit_amount" class="form-control" required>
                    </div>

                    <div class="md:col-span-2">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="vacant">Vacant</option>
                            <option value="occupied">Occupied</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-control"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="lease_agreement_file" class="form-label">Lease Agreement Document</label>
                        <input type="file" name="lease_agreement_file" id="lease_agreement_file" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="form-text text-muted">Optional: Upload a lease agreement template for this unit. It will be used when a tenant is assigned.</small>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('units.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="action-button">Save unit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
