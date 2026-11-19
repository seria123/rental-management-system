<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            if (! Schema::hasColumn('leases', 'rental_unit_id')) {
                $table->foreignId('rental_unit_id')->nullable()->after('unit_id')->constrained('units')->cascadeOnDelete();
            }
        });

        // Copy data from unit_id to rental_unit_id
        DB::table('leases')->whereNull('rental_unit_id')->update([
            'rental_unit_id' => DB::raw('unit_id'),
        ]);
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            if (Schema::hasColumn('leases', 'rental_unit_id')) {
                $table->dropForeign(['rental_unit_id']);
                $table->dropColumn('rental_unit_id');
            }
        });
    }
};
