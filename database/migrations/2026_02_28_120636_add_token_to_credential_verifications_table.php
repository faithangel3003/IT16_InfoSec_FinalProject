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
        Schema::table('credential_verifications', function (Blueprint $table) {
            $table->string('token', 64)->nullable()->after('ip_address')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credential_verifications', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
