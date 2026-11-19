<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_out_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->date('requested_move_out_date');
            $table->unsignedTinyInteger('notice_days')->default(5);
            $table->string('reason')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, completed
            $table->decimal('deposit_amount', 12, 2); // deposit from lease
            $table->decimal('deposit_return_amount', 12, 2)->default(0); // amount to return
            $table->decimal('deposit_deduction_amount', 12, 2)->default(0); // amount deducted for damages
            $table->text('damage_notes')->nullable(); // notes about damages
            $table->decimal('damage_cost', 12, 2)->default(0); // cost of damages
            $table->timestamp('owner_approved_at')->nullable();
            $table->text('owner_response_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['unit_id', 'status']);
            $table->index(['status', 'requested_move_out_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_out_requests');
    }
};
