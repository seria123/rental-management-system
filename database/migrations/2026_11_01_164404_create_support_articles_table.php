<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('support_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
        Schema::create('support_article_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('helpful');
            $table->timestamps();
            $table->unique(['support_article_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_article_ratings');
        Schema::dropIfExists('support_articles');
    }
};
