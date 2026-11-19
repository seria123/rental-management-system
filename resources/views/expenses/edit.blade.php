```blade
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Expense</h2>

        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
            Back to Expenses
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('expenses.update', $expense) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="property_id" class="form-label">Select Property</label>

                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                        <option value="">Choose Property</option>

                        @foreach(App\Models\Property::all() as $property)
                            <option value="{{ $property->id }}"
                                {{ old('property_id', $expense->property_id) == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('property_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Expense Title</label>

                    <input type="text"
                           name="title"
                           id="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $expense->title) }}"
                           required>

                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>

                    <select name="category"
                            id="category"
                            class="form-select @error('category') is-invalid @enderror"
                            required>
                        <option value="">Select a category</option>

                        @foreach(['Transport', 'Food', 'Rent', 'Utilities', 'Salaries', 'Supplies', 'Maintenance', 'Other'] as $category)
                            <option value="{{ $category }}"
                                @selected(old('category', $expense->category) === $category)>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>

                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (KSh)</label>

                    <input type="number"
                           name="amount"
                           id="amount"
                           class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount', $expense->amount) }}"
                           min="0.01"
                           step="0.01"
                           required>

                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>

                    <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                        <option value="">Select payment method</option>
                        <option value="Cash" @selected(old('payment_method', $expense->payment_method) === 'Cash')>Cash</option>
                        <option value="Mpesa" @selected(old('payment_method', $expense->payment_method) === 'Mpesa')>Mpesa</option>
                        <option value="Bank Transfer" @selected(old('payment_method', $expense->payment_method) === 'Bank Transfer')>Bank Transfer</option>
                        <option value="Cheque" @selected(old('payment_method', $expense->payment_method) === 'Cheque')>Cheque</option>
                    </select>

                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">M-Pesa Phone Number</label>

                    <input type="tel"
                           name="phone_number"
                           id="phone_number"
                           class="form-control @error('phone_number') is-invalid @enderror"
                           value="{{ old('phone_number', $expense->phone_number) }}"
                           placeholder="e.g. 0712345678">

                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="expense_date" class="form-label">Expense Date</label>

                    <input type="date"
                           name="expense_date"
                           id="expense_date"
                           class="form-control @error('expense_date') is-invalid @enderror"
                           value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                           required>

                    @error('expense_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
    <label for="description" class="form-label">
        Expense Description
    </label>

    <textarea
        name="description"
        id="description"
        class="form-control"
        rows="3"
        placeholder="Enter expense details..."
        required
    >{{ old('description') }}</textarea>
</div>

                <button type="submit" class="btn btn-primary">
                    Update Expense
                </button>

                <a href="{{ route('expenses.index') }}" class="btn btn-light">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>
@endsection
```