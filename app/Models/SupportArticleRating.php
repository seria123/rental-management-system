<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportArticleRating extends Model
{
    protected $fillable = ['support_article_id', 'user_id', 'helpful'];

    protected $casts = ['helpful' => 'boolean'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(SupportArticle::class, 'support_article_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
