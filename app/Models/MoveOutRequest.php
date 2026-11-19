<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoveOutRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'lease_id',
        'requested_move_out_date',
        'actual_move_out_date',
        'notice_days',
        'reason',
        'status',
        'deposit_amount',
        'deposit_return_amount',
        'deposit_deduction_amount',
        'damage_notes',
        'damage_cost',
        'owner_approved_at',
        'owner_response_notes',
        'processed_at',
        'inspection_scheduled_at',
        'inspection_completed_at',
        'inspector_id',
        'inspection_notes',
        'inspection_status',
    ];

    protected $casts = [
        'requested_move_out_date' => 'date',
        'actual_move_out_date' => 'date',
        'notice_days' => 'integer',
        'deposit_amount' => 'decimal:2',
        'deposit_return_amount' => 'decimal:2',
        'deposit_deduction_amount' => 'decimal:2',
        'damage_cost' => 'decimal:2',
        'owner_approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'inspection_scheduled_at' => 'datetime',
        'inspection_completed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function property()
    {
        return $this->hasOneThrough(Property::class, Unit::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isInspectionScheduled(): bool
    {
        return $this->status === 'inspection_scheduled';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isMoveOutSoon(): bool
    {
        return $this->requested_move_out_date <= now()->addDays($this->notice_days)
            && $this->requested_move_out_date >= now();
    }
}
