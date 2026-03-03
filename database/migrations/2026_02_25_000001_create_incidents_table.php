<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->enum('type', ['unauthorized_access', 'brute_force', 'suspicious_activity', 'data_breach', 'system_error', 'policy_violation', 'other'])->default('other');
            $table->string('description');
            $table->enum('status', ['open', 'investigating', 'contained', 'resolved'])->default('open');
            $table->string('ip_address')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('affected_resource')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
