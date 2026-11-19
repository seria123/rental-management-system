<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTwoFactorSetting extends Model
{
    protected $fillable = [
        'user_id',
        'is_enabled',
        'recovery_codes',
        'secret',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'recovery_codes' => 'array',
        'secret' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
