<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add reported_by field to track who reported the incident
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedBigInteger('reported_by')->nullable()->after('user_id');
            $table->string('reported_by_name')->nullable()->after('reported_by');
            $table->string('reported_by_role')->nullable()->after('reported_by_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['reported_by', 'reported_by_name', 'reported_by_role']);
        });
    }
};
