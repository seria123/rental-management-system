<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('occupation')->nullable();
            $table->foreignId('unit_id')->nullable()
                ->constrained('units')
                ->nullOnDelete();
            $table->string('month', 7)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn([
                'gender',
                'date_of_birth',
                'occupation',
                'unit_id',
                'month',
            ]);
        });
    }
};
