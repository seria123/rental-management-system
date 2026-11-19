<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityReading extends Model
{
    protected $fillable = [
        'property_id',
        'unit_id',
        'tenant_id',
        'reading_type',
        'reading_value',
        'consumed_units',
        'billing_period',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reading_value' => 'decimal:2',
            'consumed_units' => 'decimal:2',
            'billing_period' => 'date',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
