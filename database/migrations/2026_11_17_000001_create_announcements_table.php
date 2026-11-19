<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('type')->default('general'); // general, maintenance, security, meeting, utility
            $table->string('target_type')->default('all'); // all, property, unit, tenant
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();

            $table->index(['target_type', 'property_id']);
            $table->index(['created_by']);
            $table->index(['published_at', 'expires_at']);
        });

        Schema::create('announcement_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['announcement_id', 'tenant_id']);
            $table->index(['tenant_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_recipients');
        Schema::dropIfExists('announcements');
    }
};
