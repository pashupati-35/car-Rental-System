<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusColumnInBookingCarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_car', function (Blueprint $table) {
            // Change the 'status' column from ENUM to STRING
            $table->string('status', 50)->default('reserved')->change();
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
            // Revert the 'status' column back to ENUM if needed
            $table->enum('status', ['booked', 'reserved', 'canceled'])->default('reserved')->change();
        });
    }
}
