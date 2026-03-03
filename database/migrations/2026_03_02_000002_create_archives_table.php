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
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // e.g., 'supplier', 'employee', 'room', 'item', etc.
            $table->unsignedBigInteger('entity_id'); // Original ID of the deleted entity
            $table->string('entity_name'); // Name/identifier of the deleted entity
            $table->json('entity_data'); // Full JSON backup of the entity data
            $table->text('deletion_reason'); // Reason for deletion
            $table->unsignedBigInteger('deleted_by'); // User who deleted
            $table->string('deleted_by_name'); // Name of user who deleted
            $table->string('deleted_by_role'); // Role of user who deleted
            $table->timestamp('deleted_at'); // When it was deleted
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('entity_type');
            $table->index('deleted_by');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
