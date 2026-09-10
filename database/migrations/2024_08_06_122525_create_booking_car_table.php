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
            $table->string('pickup_location')->nullable();
            $table->string('drop_location')->nullable();
            $table->date('pick_up_date')->nullable();
            $table->date('last_date')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->unsignedBigInteger('car_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes()->nullable();
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
