<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarCalendarTable extends Migration
{
    public function up()
    {
        Schema::create('car_calendar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->date('date')->nullable();
            $table->string('status')->nullable()->default('available');
            $table->timestamps();
            $table->softDeletes()->nullable();

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('car_calendar');
    }
}
