<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'tenant_id',
        'lease_id',
        'rent_invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'phone_number',
        'transaction_reference',
        'transaction_id',
        'result_code',
        'result_desc',
        'checkout_request_id',
        'merchant_request_id',
        'callback_payload',
        'payment_type',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'callback_payload' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(RentInvoice::class, 'rent_invoice_id');
    }
}
