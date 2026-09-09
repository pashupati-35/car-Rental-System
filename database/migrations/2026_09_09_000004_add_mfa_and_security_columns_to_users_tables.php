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
        $tables = ['admins', 'owners', 'customers', 'users'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'is_mfa_enabled')) {
                        $table->boolean('is_mfa_enabled')->default(0)->nullable()->after('password');
                    }
                    if (!Schema::hasColumn($tableName, 'is_email_authentication_enabled')) {
                        $table->boolean('is_email_authentication_enabled')->default(0)->nullable()->after('is_mfa_enabled');
                    }
                    if (!Schema::hasColumn($tableName, 'mfa_secret_code')) {
                        $table->string('mfa_secret_code')->nullable()->after('is_email_authentication_enabled');
                    }
                    if (!Schema::hasColumn($tableName, 'mfa_authentication_image')) {
                        $table->text('mfa_authentication_image')->nullable()->after('mfa_secret_code');
                    }
                    if (!Schema::hasColumn($tableName, 'last_logged_in')) {
                        $table->dateTime('last_logged_in')->nullable()->after('mfa_authentication_image');
                    }
                    if (!Schema::hasColumn($tableName, 'is_active')) {
                        $table->boolean('is_active')->default(1)->nullable()->after('last_logged_in');
                    }
                    if (!Schema::hasColumn($tableName, 'is_login_verified')) {
                        $table->boolean('is_login_verified')->default(1)->nullable()->after('is_active');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['admins', 'owners', 'customers', 'users'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $columns = [
                        'is_mfa_enabled',
                        'is_email_authentication_enabled',
                        'mfa_secret_code',
                        'mfa_authentication_image',
                        'last_logged_in',
                        'is_active',
                        'is_login_verified',
                    ];
                    foreach ($columns as $column) {
                        if (Schema::hasColumn($tableName, $column)) {
                            $table->dropColumn($column);
                        }
                    }
                });
            }
        }
    }
};
