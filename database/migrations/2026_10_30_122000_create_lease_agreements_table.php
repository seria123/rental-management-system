<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lease_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->string('file_path'); // path to the uploaded agreement document
            $table->string('original_filename'); // original name of the uploaded file
            $table->boolean('owner_signed')->default(false);
            $table->boolean('tenant_signed')->default(false);
            $table->timestamp('owner_signed_at')->nullable();
            $table->timestamp('tenant_signed_at')->nullable();
            $table->string('owner_signature_ip')->nullable(); // IP of who signed as owner
            $table->string('tenant_signature_ip')->nullable(); // IP of who signed as tenant
            $table->timestamps();

            $table->index(['lease_id', 'owner_signed', 'tenant_signed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lease_agreements');
    }
};
