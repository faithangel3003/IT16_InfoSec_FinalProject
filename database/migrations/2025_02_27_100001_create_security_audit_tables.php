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
        // Create table for tracking credential verifications (for sensitive actions)
        Schema::create('credential_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action_type'); // e.g., 'view_sensitive_data', 'create_employee', 'delete_employee'
            $table->string('action_description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Verification valid for limited time
            $table->timestamps();
        });

        // Create table for tracking data unmask requests
        Schema::create('data_unmask_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('entity_type'); // e.g., 'employee', 'supplier'
            $table->unsignedBigInteger('entity_id');
            $table->string('field_name'); // e.g., 'contact_number', 'sss_number'
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('unmasked_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_unmask_logs');
        Schema::dropIfExists('credential_verifications');
    }
};
