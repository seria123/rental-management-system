@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Move-Out Requests</h2>
            <p class="text-muted mb-0">Manage tenant move-out requests and deposit returns.</p>
        </div>
        <a href="{{ route('move-out-requests.upcoming') }}" class="btn btn-outline-primary">
            <i class="fas fa-calendar me-2"></i> Upcoming Move-Outs (5-Day Notice)
        </a>
    </div>

    @if ($requests->isEmpty())
        <div class="text-center py-5">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-500 mb-3">
                <i class="fas fa-inbox text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-900">No move-out requests</h3>
            <p class="text-slate-500">Tenants will submit move-out requests here when they plan to vacate.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tenant</th>
                        <th>Unit</th>
                        <th>Property</th>
                        <th>Move-Out Date</th>
                        <th>Deposit</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $request->tenant->name }}</div>
                                <small class="text-muted">{{ $request->tenant->phone ?? 'No phone' }}</small>
                            </td>
                            <td>{{ $request->unit->unit_number ?? 'N/A' }}</td>
                            <td>{{ $request->unit->property->name ?? 'N/A' }}</td>
                            <td>{{ $request->requested_move_out_date?->format('d M Y') }}</td>
                            <td>KSh {{ number_format($request->deposit_amount, 2) }}</td>
                            <td>
                                <span class="badge
                                    @if($request->status === 'pending') bg-warning
                                    @elseif($request->status === 'approved') bg-success
                                    @elseif($request->status === 'rejected') bg-danger
                                    @elseif($request->status === 'completed') bg-secondary
                                    @endif
                                ">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>
                                @if ($request->isPending())
                                    <form action="{{ route('move-out-requests.approve', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this move-out request?')">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('move-out-requests.reject', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this move-out request?')">
                                            Reject
                                        </button>
                                    </form>
                                @elseif ($request->isApproved())
                                    <a href="{{ route('move-out-requests.process', $request->id) }}" class="btn btn-sm btn-primary">
                                        Process Deposit
                                    </a>
                                @elseif ($request->isCompleted())
                                    <span class="badge bg-secondary">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($requests->hasPages())
                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
