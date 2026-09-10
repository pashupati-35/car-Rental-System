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
        // 1. Add SoftDeletes to all relevant system tables
        $tablesWithSoftDeletes = ['admins', 'owners', 'customers', 'cars', 'drivers', 'booking_car', 'payments', 'email_templates'];
        foreach ($tablesWithSoftDeletes as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes()->nullable();
                });
            }
        }

        // Helper function to add all AdminUser & Employee fields to user tables (admins, owners, customers)
        $userTables = ['admins', 'owners', 'customers'];

        foreach ($userTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'unique_identifier')) {
                        $table->string('unique_identifier')->nullable()->after('id');
                    }
                    if (!Schema::hasColumn($tableName, 'first_name')) {
                        $table->string('first_name')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'middle_name')) {
                        $table->string('middle_name')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'last_name')) {
                        $table->string('last_name')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'image')) {
                        $table->string('image')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'mobile')) {
                        $table->string('mobile')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'phone')) {
                        $table->string('phone')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'contact_number')) {
                        $table->string('contact_number')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'address')) {
                        $table->string('address')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'username')) {
                        $table->string('username')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'date_of_birth')) {
                        $table->date('date_of_birth')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'gender')) {
                        $table->string('gender')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'marital_status')) {
                        $table->string('marital_status')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'nationality')) {
                        $table->string('nationality')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'citizenship_number')) {
                        $table->string('citizenship_number')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'passport_number')) {
                        $table->string('passport_number')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'position')) {
                        $table->string('position')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'designation')) {
                        $table->string('designation')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'user_type')) {
                        $table->string('user_type')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'access_type')) {
                        $table->string('access_type')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'has_email_access')) {
                        $table->boolean('has_email_access')->nullable()->default(false);
                    }
                    if (!Schema::hasColumn($tableName, 'access_email_type')) {
                        $table->string('access_email_type')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'approval_status')) {
                        $table->string('approval_status')->nullable()->default('approved');
                    }
                    if (!Schema::hasColumn($tableName, 'register_type')) {
                        $table->string('register_type')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'is_submitted')) {
                        $table->boolean('is_submitted')->nullable()->default(true);
                    }
                    if (!Schema::hasColumn($tableName, 'theme_style')) {
                        $table->string('theme_style')->nullable()->default('light');
                    }
                    if (!Schema::hasColumn($tableName, 'emergency_contact')) {
                        $table->string('emergency_contact')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'contact_person_name')) {
                        $table->string('contact_person_name')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'contact_relationship')) {
                        $table->string('contact_relationship')->nullable();
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
        $tablesWithSoftDeletes = ['admins', 'owners', 'customers', 'cars', 'drivers', 'booking_car', 'payments', 'email_templates'];
        foreach ($tablesWithSoftDeletes as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
