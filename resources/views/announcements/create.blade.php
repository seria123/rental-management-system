@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-4">
    <div class="panel p-8 rounded-2xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">New Announcement</p>
                <h1 class="text-2xl font-bold text-slate-900">Publish Announcement</h1>
            </div>
            <a href="{{ route('announcements.index') }}"
                class="text-sm text-indigo-600 hover:underline">
                Back to Announcements
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

        <form method="POST" action="{{ route('announcements.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block font-semibold mb-2">Title</label>
                    <input type="text" name="title"
                        value="{{ old('title') }}"
                        required maxlength="255"
                        class="w-full rounded-xl border-gray-300 px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Type</label>
                    <select name="type" class="w-full rounded-xl border-gray-300 px-4 py-3">
                        <option value="general" {{ old('type', 'general') === 'general' ? 'selected' : '' }}>General</option>
                        <option value="maintenance" {{ old('type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="security" {{ old('type') === 'security' ? 'selected' : '' }}>Security</option>
                        <option value="meeting" {{ old('type') === 'meeting' ? 'selected' : '' }}>Property Meeting</option>
                        <option value="utility" {{ old('type') === 'utility' ? 'selected' : '' }}>Utility Notice</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-semibold mb-2">Target Audience</label>
                    <select name="target_type" id="target_type"
                        class="w-full rounded-xl border-gray-300 px-4 py-3">
                        <option value="all" {{ old('target_type', 'all') === 'all' ? 'selected' : '' }}>All Tenants</option>
                        <option value="property" {{ old('target_type') === 'property' ? 'selected' : '' }}>Specific Property</option>
                        <option value="unit" {{ old('target_type') === 'unit' ? 'selected' : '' }}>Specific Unit</option>
                        <option value="tenant" {{ old('target_type') === 'tenant' ? 'selected' : '' }}>Specific Tenant</option>
                    </select>
                </div>
            </div>

            <!-- Property selector (shown when target_type = property) -->
            <div id="property_selector" {{ old('target_type', 'all') !== 'property' ? 'class="hidden"' : '' }}
                 class="mb-6">
                <label class="block font-semibold mb-2">Property</label>
                <select name="property_id" class="w-full rounded-xl border-gray-300 px-4 py-3">
                    <option value="">Select property</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Unit selector (shown when target_type = unit) -->
            <div id="unit_selector" class="hidden mb-6">
                <label class="block font-semibold mb-2">Unit</label>
                <select name="unit_id" class="w-full rounded-xl border-gray-300 px-4 py-3">
                    <option value="">Select unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->unit_number }} - {{ $unit->property->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tenant selector (shown when target_type = tenant) -->
            <div id="tenant_selector" class="hidden mb-6">
                <label class="block font-semibold mb-2">Tenant</label>
                <select name="tenant_id" class="w-full rounded-xl border-gray-300 px-4 py-3">
                    <option value="">Select tenant</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                            {{ $tenant->name }} - {{ $tenant->unit?->unit_number ?? 'No Unit' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block font-semibold mb-2">Content</label>
                <textarea name="content" rows="8" required
                    class="w-full rounded-xl border-gray-300 px-4 py-3">{{ old('content') }}</textarea>
                <p class="text-xs text-slate-500 mt-1">
                    Describe the announcement. This will be sent to the selected recipients.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block font-semibold mb-2">Publish At</label>
                    <input type="datetime-local" name="published_at"
                        value="{{ old('published_at') }}"
                        class="w-full rounded-xl border-gray-300 px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Expires At</label>
                    <input type="datetime-local" name="expires_at"
                        value="{{ old('expires_at') }}"
                        class="w-full rounded-xl border-gray-300 px-4 py-3">
                </div>
            </div>

            <div class="mb-6 flex items-start gap-3">
                <input type="checkbox" name="is_pinned" id="is_pinned"
                    {{ old('is_pinned') ? 'checked' : '' }}
                    class="mt-1 rounded text-indigo-600 focus:ring-indigo-500">
                <label for="is_pinned" class="text-sm text-slate-600">
                    Pin this announcement (appears at top of list)
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold">
                    Publish Announcement
                </button>
                <a href="{{ route('announcements.index') }}"
                    class="flex-1 border border-slate-300 text-slate-700 px-6 py-3 rounded-xl hover:bg-slate-50 text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('target_type')?.addEventListener('change', function(e) {
        const type = e.target.value;
        document.getElementById('property_selector').classList.add('hidden');
        document.getElementById('unit_selector').classList.add('hidden');
        document.getElementById('tenant_selector').classList.add('hidden');

        if (type === 'property') {
            document.getElementById('property_selector').classList.remove('hidden');
        } else if (type === 'unit') {
            document.getElementById('unit_selector').classList.remove('hidden');
        } else if (type === 'tenant') {
            document.getElementById('tenant_selector').classList.remove('hidden');
        }
    });
</script>
@endpush