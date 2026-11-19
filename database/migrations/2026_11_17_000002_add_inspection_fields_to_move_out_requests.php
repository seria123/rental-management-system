<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('move_out_requests', function (Blueprint $table) {
            $table->timestamp('inspection_scheduled_at')->nullable()->after('owner_approved_at');
            $table->timestamp('inspection_completed_at')->nullable();
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete()->after('inspection_completed_at');
            $table->text('inspection_notes')->nullable()->after('inspector_id');
            $table->string('inspection_status')->nullable()->after('inspection_notes'); // scheduled, in_progress, completed
        });
    }

    public function down(): void
    {
        Schema::table('move_out_requests', function (Blueprint $table) {
            $table->dropColumn([
                'inspection_scheduled_at',
                'inspection_completed_at',
                'inspector_id',
                'inspection_notes',
                'inspection_status',
            ]);
        });
    }
};
