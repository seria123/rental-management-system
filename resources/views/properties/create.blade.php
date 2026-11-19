@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-indigo-600">Portfolio</p>
            <h1 class="page-title">Add property</h1>
        </div>
        <a href="{{ route('properties.index') }}" class="ghost-button">Back</a>
    </div>

    <div class="panel p-6">
        <form action="{{ route('properties.store') }}" method="POST" class="space-y-6" id="propertyForm">
            @csrf

            <!-- Property Details -->
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="property_owner_id" class="form-label">Owner</label>
                    <select name="property_owner_id" id="property_owner_id" class="form-select" required>
                        <option value="">Select owner</option>
                        @foreach(App\Models\PropertyOwner::all() as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="name" class="form-label">Property name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div>
                    <label for="property_type" class="form-label">Property type</label>
                    <select name="property_type" id="property_type" class="form-select" required>
                        <option value="">Select type</option>
                        <option value="Apartment">Apartment</option>
                        <option value="House">House</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Office">Office</option>
                    </select>
                </div>

                <div>
                    <label for="city" class="form-label">City</label>
                    <input type="text" name="city" id="city" class="form-control" required>
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="form-label">Address</label>
                    <textarea name="address" id="address" rows="3" class="form-control" required></textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control"></textarea>
                </div>

                <div>
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Units Section -->
            <div class="border-t border-slate-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">Units</h3>
                    <button type="button" id="addUnitBtn" class="action-button btn-sm">
                        <span>+ Add Unit</span>
                    </button>
                </div>

                <div id="unitsContainer">
                    @for ($i = 0; $i < 1; $i++)
                        <div class="unit-row grid gap-4 md:grid-cols-6 border border-slate-200 rounded-xl p-4 mb-4 bg-slate-50" data-unit-index="{{ $i }}">
                            <div>
                                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Unit Number</label>
                                <input type="text" name="units[{{ $i }}][unit_number]" class="form-control" required placeholder="e.g. A-101">
                            </div>
                            <div>
                                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Unit Type</label>
                                <select name="units[{{ $i }}][unit_type]" class="form-select" required>
                                    <option value="">Select type</option>
                                    <option value="studio">Studio</option>
                                    <option value="one_bedroom">1 Bedroom</option>
                                    <option value="two_bedroom">2 Bedroom</option>
                                    <option value="three_bedroom">3 Bedroom</option>
                                    <option value="commercial">Commercial</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Floor</label>
                                <input type="text" name="units[{{ $i }}][floor]" class="form-control" placeholder="Ground">
                            </div>
                            <div>
                                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Monthly Rent (KSh)</label>
                                <input type="number" step="0.01" min="0" name="units[{{ $i }}][monthly_rent]" class="form-control" required placeholder="0.00">
                            </div>
                            <div>
                                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Deposit (KSh)</label>
                                <input type="number" step="0.01" min="0" name="units[{{ $i }}][deposit_amount]" class="form-control" required placeholder="0.00">
                            </div>
                            <div class="flex items-end">
                                <select name="units[{{ $i }}][status]" class="form-select">
                                    <option value="vacant">Vacant</option>
                                    <option value="occupied">Occupied</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="mt-3 text-xs text-slate-500">
                    Each unit can have its own rent and deposit amount. These values will be used when a tenant is assigned.
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('properties.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="action-button">Save property &amp; units</button>
            </div>
        </form>
    </div>
</div>

<script>
    let unitCount = 1;

    document.getElementById('addUnitBtn').addEventListener('click', function () {
        const container = document.getElementById('unitsContainer');
        const unitRow = document.createElement('div');
        unitRow.className = 'unit-row grid gap-4 md:grid-cols-6 border border-slate-200 rounded-xl p-4 mb-4 bg-slate-50';
        unitRow.setAttribute('data-unit-index', unitCount);

        unitRow.innerHTML = `
            <div>
                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Unit Number</label>
                <input type="text" name="units[${unitCount}][unit_number]" class="form-control" required placeholder="e.g. A-101">
            </div>
            <div>
                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Unit Type</label>
                <select name="units[${unitCount}][unit_type]" class="form-select" required>
                    <option value="">Select type</option>
                    <option value="studio">Studio</option>
                    <option value="one_bedroom">1 Bedroom</option>
                    <option value="two_bedroom">2 Bedroom</option>
                    <option value="three_bedroom">3 Bedroom</option>
                    <option value="commercial">Commercial</option>
                </select>
            </div>
            <div>
                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Floor</label>
                <input type="text" name="units[${unitCount}][floor]" class="form-control" placeholder="Ground">
            </div>
            <div>
                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Monthly Rent (KSh)</label>
                <input type="number" step="0.01" min="0" name="units[${unitCount}][monthly_rent]" class="form-control" required placeholder="0.00">
            </div>
            <div>
                <label class="form-label block text-xs uppercase text-slate-500 mb-1">Deposit (KSh)</label>
                <input type="number" step="0.01" min="0" name="units[${unitCount}][deposit_amount]" class="form-control" required placeholder="0.00">
            </div>
            <div class="flex items-end">
                <select name="units[${unitCount}][status]" class="form-select">
                    <option value="vacant">Vacant</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                </select>
                <button type="button" onclick="this.closest('.unit-row').remove()" class="ml-2 text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
            </div>
        `;

        container.appendChild(unitRow);
        unitCount++;
    });
</script>
@endsection
