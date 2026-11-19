<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'id_number',
        'phone',
        'email',
        'national_id',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'gender',
        'date_of_birth',
        'occupation',
        'unit_id',
        'month',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function currentLease(): HasOne
    {
        return $this->hasOne(Lease::class)->where('status', 'active')->orderByDesc('start_date');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function moveOutRequests(): HasMany
    {
        return $this->hasMany(MoveOutRequest::class);
    }
}
