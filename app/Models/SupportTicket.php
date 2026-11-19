<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = ['ticket_number', 'user_id', 'category_id', 'subject', 'description', 'priority', 'status', 'assigned_to', 'resolved_at'];

    protected $casts = ['resolved_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SupportCategory::class, 'category_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->assignee();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class);
    }

    public function replies(): HasMany
    {
        return $this->messages();
    }
}
