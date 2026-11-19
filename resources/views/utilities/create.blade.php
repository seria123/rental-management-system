@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Operations</p>
                <h1 class="page-title">Add utility bill</h1>
            </div>
            <a href="{{ route('utilities.index') }}" class="ghost-button">Back</a>
        </div>

        <div class="panel p-6">
            <form action="{{ route('utilities.store') }}" method="POST" class="space-y-5">
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
                        <label for="unit_id" class="form-label">Unit</label>
                        <select name="unit_id" id="unit_id" class="form-select">
                            <option value="">Select unit</option>
                            @foreach($properties as $property)
                                @foreach($property->units as $unit)
                                    <option value="{{ $unit->id }}">{{ $property->name }} - {{ $unit->unit_number }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tenant_id" class="form-label">Tenant</label>
                        <select name="tenant_id" id="tenant_id" class="form-select">
                            <option value="">Select tenant</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="bill_type" class="form-label">Bill type</label>
                        <select name="bill_type" id="bill_type" class="form-select" required>
                            <option value="water">Water</option>
                            <option value="electricity">Electricity</option>
                            <option value="garbage">Garbage</option>
                            <option value="security">Security</option>
                            <option value="gas">Gas</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="billing_period" class="form-label">Billing period</label>
                        <input type="month" name="billing_period" id="billing_period" class="form-control" required>
                    </div>

                    <div>
                        <label for="due_date" class="form-label">Due date</label>
                        <input type="date" name="due_date" id="due_date" class="form-control" required>
                    </div>

                    <div>
                        <label for="reading_value" class="form-label">Meter reading</label>
                        <input type="number" step="0.01" min="0" name="reading_value" id="reading_value" class="form-control">
                    </div>

                    <div>
                        <label for="consumed_units" class="form-label">Consumed units</label>
                        <input type="number" step="0.01" min="0" name="consumed_units" id="consumed_units" class="form-control">
                    </div>

                    <div>
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" required>
                    </div>

                    <div>
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('utilities.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="action-button">Save utility bill</button>
                </div>
            </form>
        </div>
    </div>
@endsection
