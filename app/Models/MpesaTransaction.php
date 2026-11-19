<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaTransaction extends Model
{
    protected $fillable = [
        'tenant_id',
        'transaction_id',
        'amount',
        'checkout_request_id',
        'merchant_request_id',
        'result_code',
        'result_desc',
        'transaction_type',
        'status',
        'payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payload' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
