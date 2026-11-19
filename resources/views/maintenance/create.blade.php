@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-4">
    <div class="panel p-8 rounded-2xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">New Maintenance Request</p>
                <h1 class="text-2xl font-bold text-slate-900">Create Maintenance Request</h1>
            </div>
            <a href="{{ route('maintenance.index') }}"
                class="text-sm text-indigo-600 hover:underline">
                Back to Maintenance
            </a>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 p-4 rounded-xl mb-6">
                <ul class="list-disc list-inside text-sm text-red-800 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('maintenance.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block font-semibold mb-2">Property</label>
                    <select name="property_id" id="property_id" required
                        class="w-full rounded-xl border-gray-300 px-4 py-3">
                        <option value="">Select property</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Unit</label>
                    <select name="unit_id" id="unit_id" required
                        class="w-full rounded-xl border-gray-300 px-4 py-3">
                        <option value="">Select unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" data-property-id="{{ $unit->property_id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->unit_number }} ({{ $unit->property->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Tenant</label>
                    <select name="tenant_id" id="tenant_id"
                        class="w-full rounded-xl border-gray-300 px-4 py-3">
                        <option value="">No specific tenant</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                {{ $tenant->name }} - {{ $tenant->unit?->unit_number ?? 'No Unit' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Priority</label>
                    <select name="priority" class="w-full rounded-xl border-gray-300 px-4 py-3">
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block font-semibold mb-2">Title</label>
                <input type="text" name="title" required maxlength="255"
                    value="{{ old('title') }}"
                    class="w-full rounded-xl border-gray-300 px-4 py-3">
            </div>

            <div class="mb-6">
                <label class="block font-semibold mb-2">Description</label>
                <textarea name="description" rows="6" required
                    class="w-full rounded-xl border-gray-300 px-4 py-3">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold">
                    Create Request
                </button>
                <a href="{{ route('maintenance.index') }}"
                    class="flex-1 border border-slate-300 text-slate-700 px-6 py-3 rounded-xl hover:bg-slate-50 text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const propertySelect = document.getElementById('property_id');
    const unitSelect = document.getElementById('unit_id');

    if (propertySelect && unitSelect) {
        const allUnits = Array.from(unitSelect.options).slice(1);

        propertySelect.addEventListener('change', function() {
            const propertyId = this.value;

            unitSelect.innerHTML = '<option value="">Select unit</option>';

            allUnits.forEach(option => {
                if (!propertyId || option.dataset.propertyId === propertyId) {
                    unitSelect.appendChild(option.cloneNode(true));
                }
            });
        });
    }
</script>
@endpush