<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reading_type');
            $table->decimal('reading_value', 12, 2)->default(0);
            $table->decimal('consumed_units', 12, 2)->default(0);
            $table->date('billing_period');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('utility_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('bill_type');
            $table->date('billing_period');
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_bills');
        Schema::dropIfExists('utility_readings');
    }
};
