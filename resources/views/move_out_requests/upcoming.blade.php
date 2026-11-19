@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Upcoming Move-Outs</h2>
            <p class="text-muted mb-0">Tenants moving out within the next 5 days. You will be notified in advance.</p>
        </div>
        <a href="{{ route('move-out-requests.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to All Requests
        </a>
    </div>

    @if ($upcoming->isEmpty())
        <div class="text-center py-5">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-500 mb-3">
                <i class="fas fa-calendar-check text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-900">No upcoming move-outs</h3>
            <p class="text-slate-500">No tenants are scheduled to move out within the next 5 days.</p>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-bell me-2"></i>
            <strong>Advance Notice:</strong> The following tenants are moving out within the next 5 days. Please prepare for unit inspection and deposit processing.
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tenant</th>
                        <th>Unit</th>
                        <th>Property</th>
                        <th>Move-Out Date</th>
                        <th>Days Remaining</th>
                        <th>Deposit</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcoming as $request)
                        <?php
                        $daysRemaining = now()->diffInDays($request->requested_move_out_date, false);
                        ?>
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $request->tenant->name }}</div>
                                <small class="text-muted">{{ $request->tenant->phone ?? 'No phone' }}</small>
                            </td>
                            <td>{{ $request->unit->unit_number ?? 'N/A' }}</td>
                            <td>{{ $request->unit->property->name ?? 'N/A' }}</td>
                            <td>{{ $request->requested_move_out_date?->format('d M Y') }}</td>
                            <td>
                                <span class="badge
                                    @if($daysRemaining <= 2) bg-danger
                                    @elseif($daysRemaining <= 5) bg-warning
                                    @else bg-info
                                    @endif
                                ">
                                    {{ $daysRemaining }} day{{ $daysRemaining != 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td>KSh {{ number_format($request->deposit_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-success">{{ ucfirst($request->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('move-out-requests.process', $request->id) }}" class="btn btn-sm btn-primary">
                                    Process Deposit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
