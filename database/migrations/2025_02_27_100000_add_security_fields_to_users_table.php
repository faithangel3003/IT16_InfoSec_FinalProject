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
        Schema::table('users', function (Blueprint $table) {
            // Account lockout fields
            $table->integer('failed_login_attempts')->default(0)->after('role');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->timestamp('last_failed_login_at')->nullable()->after('locked_until');
            
            // For notifying user of failed attempts on next successful login
            $table->boolean('has_unnotified_failed_attempts')->default(false)->after('last_failed_login_at');
            
            // Password policy fields
            $table->timestamp('password_changed_at')->nullable()->after('has_unnotified_failed_attempts');
            
            // Last login tracking
            $table->timestamp('last_login_at')->nullable()->after('password_changed_at');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'failed_login_attempts',
                'locked_until',
                'last_failed_login_at',
                'has_unnotified_failed_attempts',
                'password_changed_at',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
