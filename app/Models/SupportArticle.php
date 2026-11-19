<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportArticle extends Model
{
    protected $fillable = ['category_id', 'title', 'slug', 'content', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SupportCategory::class, 'category_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(SupportArticleRating::class);
    }
}
