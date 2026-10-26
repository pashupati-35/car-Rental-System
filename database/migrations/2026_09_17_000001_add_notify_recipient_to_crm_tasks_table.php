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
        if (Schema::hasTable('crm_tasks')) {
            Schema::table('crm_tasks', function (Blueprint $table) {
                if (! Schema::hasColumn('crm_tasks', 'notify_recipient')) {
                    $table->boolean('notify_recipient')->default(false)->after('priority');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('crm_tasks')) {
            Schema::table('crm_tasks', function (Blueprint $table) {
                if (Schema::hasColumn('crm_tasks', 'notify_recipient')) {
                    $table->dropColumn('notify_recipient');
                }
            });
        }
    }
};
