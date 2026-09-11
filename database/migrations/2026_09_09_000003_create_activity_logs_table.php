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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_type', 50);                      // login, logout, create, update, delete, restore, export, import
            $table->string('description')->nullable();            // Human-readable description
            $table->nullableMorphs('causer');                     // causer_type + causer_id (AdminUser, Employee, User)
            $table->nullableMorphs('subject');                    // subject_type + subject_id (the affected model)
            $table->string('ip_address', 45)->nullable();         // Visitor's IP address
            $table->text('user_agent')->nullable();               // Browser user agent
            $table->json('properties')->nullable();               // JSON data — old/new values, request data
            $table->string('table_name', 100)->nullable();        // Affected database table name
            $table->timestamps();
            $table->softDeletes()->nullable();

            $table->index('log_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
