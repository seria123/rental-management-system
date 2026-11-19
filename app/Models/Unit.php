<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    protected $table = 'units';

    protected $fillable = [
        'property_id',
        'unit_number',
        'unit_type',
        'floor',
        'monthly_rent',
        'deposit_amount',
        'status',
        'description',
        'lease_agreement_path',
        'lease_agreement_filename',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'unit_id');
    }

    public function activeLease(): HasOne
    {
        return $this->hasOne(Lease::class, 'unit_id')->where('status', 'active')->orderByDesc('start_date');
    }

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Lease::class, 'unit_id', 'id', 'id', 'id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}
