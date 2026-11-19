<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('in_app_notifications')->default(true);
            $table->boolean('rent_payment_notifications')->default(true);
            $table->boolean('maintenance_notifications')->default(true);
            $table->boolean('system_announcements')->default(true);
            $table->timestamps();

            $table->unique(['user_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};
