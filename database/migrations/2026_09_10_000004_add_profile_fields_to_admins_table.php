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
        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('email');
            }
            if (! Schema::hasColumn('admins', 'address')) {
                $table->string('address')->nullable()->after('contact_number');
            }
            if (! Schema::hasColumn('admins', 'designation')) {
                $table->string('designation')->nullable()->after('address');
            }
            if (! Schema::hasColumn('admins', 'avatar')) {
                $table->string('avatar')->nullable()->after('designation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['contact_number', 'address', 'designation', 'avatar']);
        });
    }
};
