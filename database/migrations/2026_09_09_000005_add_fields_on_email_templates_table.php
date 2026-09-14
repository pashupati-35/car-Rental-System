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
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('message_data')->nullable()->after('type');
            $table->string('info_message')->nullable()->after('message_data');
            $table->string('alert_message')->nullable()->after('info_message');
            $table->string('cta_url')->nullable()->after('alert_message');
            $table->string('cta_text')->nullable()->after('cta_url');
            $table->string('secondary_cta_url')->nullable()->after('cta_text');
            $table->string('secondary_cta_text')->nullable()->after('secondary_cta_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn([
                'message_data',
                'info_message',
                'alert_message',
                'cta_url',
                'cta_text',
                'secondary_cta_url',
                'secondary_cta_text',
            ]);
        });
    }
};
