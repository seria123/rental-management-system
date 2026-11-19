<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lease_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rent_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method');
            $table->string('transaction_reference')->nullable();
            $table->string('payment_type')->default('rent');
            $table->string('status')->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('transaction_reference');
            $table->index(['tenant_id', 'payment_date']);
            $table->index(['lease_id', 'payment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
