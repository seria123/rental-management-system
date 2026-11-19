<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('transaction_reference');
            }
            if (! Schema::hasColumn('payments', 'result_code')) {
                $table->string('result_code')->nullable()->after('transaction_id');
            }
            if (! Schema::hasColumn('payments', 'result_desc')) {
                $table->text('result_desc')->nullable()->after('result_code');
            }
            if (! Schema::hasColumn('payments', 'checkout_request_id')) {
                $table->string('checkout_request_id')->nullable()->after('result_desc');
            }
            if (! Schema::hasColumn('payments', 'merchant_request_id')) {
                $table->string('merchant_request_id')->nullable()->after('checkout_request_id');
            }
            if (! Schema::hasColumn('payments', 'callback_payload')) {
                $table->json('callback_payload')->nullable()->after('merchant_request_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $cols = ['transaction_id', 'result_code', 'result_desc', 'checkout_request_id', 'merchant_request_id', 'callback_payload'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};