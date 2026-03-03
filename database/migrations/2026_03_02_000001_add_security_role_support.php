<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration adds support for the 'security' role.
     * The role field already exists, this ensures the system recognizes 'security' as valid.
     */
    public function up(): void
    {
        // No schema changes needed - role is stored as string
        // This migration serves as documentation that 'security' is now a valid role
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes to reverse
    }
};
