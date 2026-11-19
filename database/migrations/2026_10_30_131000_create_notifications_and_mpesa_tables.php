<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info');
            $table->string('channel')->default('in_app');
            $table->boolean('is_read')->default(false);
            $table->string('related_model')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['tenant_id', 'is_read']);
        });

        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_id')->unique()->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('checkout_request_id')->nullable();
            $table->string('merchant_request_id')->nullable();
            $table->string('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('transaction_type')->default('paybill');
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['transaction_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
        Schema::dropIfExists('app_notifications');
    }
};
