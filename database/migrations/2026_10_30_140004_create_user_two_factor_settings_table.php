<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_two_factor_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_enabled')->default(false);
            $table->string('recovery_codes')->nullable(); // JSON array
            $table->string('secret')->nullable(); // encrypted
            $table->timestamps();

            $table->unique(['user_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_two_factor_settings');
    }
};
