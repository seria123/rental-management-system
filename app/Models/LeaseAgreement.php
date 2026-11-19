<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseAgreement extends Model
{
    protected $fillable = [
        'lease_id',
        'file_path',
        'original_filename',
        'owner_signed',
        'tenant_signed',
        'owner_signed_at',
        'tenant_signed_at',
        'owner_signature_ip',
        'tenant_signature_ip',
    ];

    protected $casts = [
        'owner_signed' => 'boolean',
        'tenant_signed' => 'boolean',
        'owner_signed_at' => 'datetime',
        'tenant_signed_at' => 'datetime',
    ];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->hasOneThrough(Tenant::class, Lease::class);
    }

    public function property(): BelongsTo
    {
        return $this->hasOneThrough(Property::class, Lease::class);
    }

    public function isFullySigned(): bool
    {
        return $this->owner_signed && $this->tenant_signed;
    }

    public function isSignedBy(string $userRole): bool
    {
        return $userRole === 'owner' ? $this->owner_signed : $this->tenant_signed;
    }
}
