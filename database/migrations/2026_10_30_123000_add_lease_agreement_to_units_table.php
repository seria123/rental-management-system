<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->string('lease_agreement_path')->nullable()->after('description');
            $table->string('lease_agreement_filename')->nullable()->after('lease_agreement_path');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['lease_agreement_path', 'lease_agreement_filename']);
        });
    }
};
