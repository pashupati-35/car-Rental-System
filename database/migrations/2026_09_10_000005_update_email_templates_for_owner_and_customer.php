<?php

use App\Models\EmailTemplate\EmailTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('email_templates')) {
            EmailTemplate::where('role', 'employee')->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
