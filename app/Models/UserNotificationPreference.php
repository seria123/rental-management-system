<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_notifications',
        'sms_notifications',
        'in_app_notifications',
        'rent_payment_notifications',
        'maintenance_notifications',
        'system_announcements',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'in_app_notifications' => 'boolean',
        'rent_payment_notifications' => 'boolean',
        'maintenance_notifications' => 'boolean',
        'system_announcements' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
