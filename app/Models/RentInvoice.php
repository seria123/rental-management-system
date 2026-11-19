<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentInvoice extends Model
{
    protected $fillable = [
        'lease_id',
        'tenant_id',
        'billing_month',
        'amount',
        'amount_paid',
        'balance',
        'due_date',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'billing_month' => 'date',
            'amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'balance' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
