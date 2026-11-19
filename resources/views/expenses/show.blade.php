
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Expense Details</h2>

        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
            Back to Expenses
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ $expense->title }}</h5>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Expense ID</div>
                <div class="col-md-8">{{ $expense->id }}</div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Title</div>
                <div class="col-md-8">{{ $expense->title }}</div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Category</div>
                <div class="col-md-8">
                    <span class="badge bg-info text-dark">
                        {{ $expense->category }}
                    </span>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Amount</div>
                <div class="col-md-8 text-danger fw-bold fs-4">
                    KSh {{ number_format((float) $expense->amount, 2) }}
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Expense Date</div>
                <div class="col-md-8">
                    {{ $expense->expense_date->format('d F Y') }}
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Description</div>
                <div class="col-md-8">
                    {{ $expense->description ?: 'No description provided.' }}
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Created At</div>
                <div class="col-md-8">
                    {{ $expense->created_at->format('d M Y, H:i') }}
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('expenses.edit', $expense) }}"
                   class="btn btn-warning">
                    Edit Expense
                </a>

                <form action="{{ route('expenses.destroy', $expense) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this expense?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">
                        Delete Expense
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
```