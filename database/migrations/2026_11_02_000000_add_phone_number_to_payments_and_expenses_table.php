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
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('payment_method');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
        });
    }
};
