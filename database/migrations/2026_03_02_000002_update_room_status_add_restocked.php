<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Updates room status logic:
     * - 'empty' = Room has no items
     * - 'restocked' = Room has items assigned (ready for guest)
     * - 'occupied' = Room has a guest
     */
    public function up(): void
    {
        // First, update existing 'occupied' status to 'restocked'
        // since old 'occupied' meant items were assigned
        DB::table('rooms')
            ->where('status', 'occupied')
            ->update(['status' => 'restocked']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'restocked' back to 'occupied' for backward compatibility
        DB::table('rooms')
            ->where('status', 'restocked')
            ->update(['status' => 'occupied']);
    }
};
