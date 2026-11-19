<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'property_id',
        'unit_id',
        'title',
        'description',
        'category',
        'amount',
        'expense_date',
        'payment_method',
        'phone_number',
        'reference',
        'notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
