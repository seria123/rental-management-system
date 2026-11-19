<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_owner_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('property_type');
            $table->text('address');
            $table->string('city');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['property_owner_id', 'status']);
            $table->index(['city', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
