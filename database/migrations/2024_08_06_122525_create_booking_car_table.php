<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingCarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_car', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_location');
            $table->string('drop_location');
            $table->date('pick_up_date');
            $table->date('last_date');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'confirm', 'cancel']);
            $table->unsignedBigInteger('car_id');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_car');
    }
}
