<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'phone',
        'gender',
        'date_of_birth',
        'national_id',
        'physical_address',
        'profile_picture_path',
        'business_name',
        'kra_pin',
        'agency_name',
        'assigned_property',
        'emergency_contact_name',
        'emergency_contact_phone',
        'payment_details',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'payment_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
