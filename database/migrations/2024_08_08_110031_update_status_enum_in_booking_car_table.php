<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusEnumInBookingCarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_car', function (Blueprint $table) {
            $table->enum('status', ['booked', 'reserved', 'canceled'])->default('reserved')->change();
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
            $table->enum('status', ['pending', 'confirm', 'cancel'])->default('pending')->change();
        });
    }
}
