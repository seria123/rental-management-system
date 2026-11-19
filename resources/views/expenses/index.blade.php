```blade
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Expense Management</h2>
            <p class="text-muted">Track and manage your expenses.</p>
        </div>

        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            + Add Expense
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            Please check the form for errors.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $expense->title }}</td>
                                <td>{{ $expense->category }}</td>
                                <td>
                                    KSh {{ number_format((float) $expense->amount, 2) }}
                                </td>
                                <td>
                                    {{ $expense->expense_date->format('d M Y') }}
                                </td>
                                <td>
                                    <a href="{{ route('expenses.show', $expense) }}"
                                       class="btn btn-sm btn-info">
                                        View
                                    </a>

                                    <a href="{{ route('expenses.edit', $expense) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('expenses.destroy', $expense) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No expenses recorded yet.
                                    <a href="{{ route('expenses.create') }}">
                                        Add your first expense
                                    </a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $expenses->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
```