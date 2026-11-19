@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Portfolio</p>
                <h1 class="page-title">Edit property</h1>
            </div>
            <a href="{{ route('properties.index') }}" class="ghost-button">Back</a>
        </div>

        <div class="panel p-6">
            <form action="{{ route('properties.update', $property) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="name" class="form-label">Property name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $property->name) }}" class="form-control" required>
                    </div>
                    <div>
                        <label for="property_type" class="form-label">Property type</label>
                        <select name="property_type" id="property_type" class="form-select" required>
                            @foreach(['Apartment','House','Commercial','Office'] as $type)
                                <option value="{{ $type }}" @selected(old('property_type', $property->property_type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" rows="3" class="form-control" required>{{ old('address', $property->address) }}</textarea>
                    </div>
                    <div>
                        <label for="city" class="form-label">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $property->city) }}" class="form-control" required>
                    </div>
                    <div>
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            @foreach(['active','inactive'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $property->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $property->description) }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <a href="{{ route('properties.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="action-button">Update property</button>
                </div>
            </form>
        </div>
    </div>
@endsection
