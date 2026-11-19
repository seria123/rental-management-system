<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'category_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(SupportArticle::class, 'category_id');
    }
}
