<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->date('billing_month');
            $table->decimal('amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->date('due_date');
            $table->string('status')->default('unpaid');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['lease_id', 'billing_month']);
            $table->index(['tenant_id', 'billing_month']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_invoices');
    }
};
