<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('username')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id')->nullable();
            $table->text('physical_address')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->string('business_name')->nullable();
            $table->string('kra_pin')->nullable();
            $table->string('agency_name')->nullable();
            $table->string('assigned_property')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('payment_details')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
