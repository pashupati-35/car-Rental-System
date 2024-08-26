<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurposeAndOtherPurposeToBookingCarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_car', function (Blueprint $table) {
            $table->string('purpose')->nullable()->after('status');
            $table->string('other_purpose')->nullable()->after('purpose');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_car', function (Blueprint $table) {
            $table->dropColumn(['purpose', 'other_purpose']);
        });
    }
}
