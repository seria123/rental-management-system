<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'target_type',
        'property_id',
        'unit_id',
        'tenant_id',
        'created_by',
        'published_at',
        'expires_at',
        'is_pinned',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function targetTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AnnouncementRecipient::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        })->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now());
        });
    }

    public function scopeForTenant($query, Tenant $tenant)
    {
        return $query->active()->where(function ($q) use ($tenant) {
            // Announcements for all tenants
            $q->where('target_type', 'all')
                // Announcements for the tenant's property
                ->orWhere(function ($q2) use ($tenant) {
                    $q2->where('target_type', 'property')
                        ->where('property_id', $tenant->unit?->property_id);
                })
                // Announcements for the tenant's unit
                ->orWhere(function ($q2) use ($tenant) {
                    $q2->where('target_type', 'unit')
                        ->where('unit_id', $tenant->unit_id);
                })
                // Announcements specifically for this tenant
                ->orWhere(function ($q2) use ($tenant) {
                    $q2->where('target_type', 'tenant')
                        ->where('tenant_id', $tenant->id);
                });
        });
    }
}
