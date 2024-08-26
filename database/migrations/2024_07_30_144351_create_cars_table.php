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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('car_name');
            $table->string('car_model');
            $table->string('car_number')->unique();
            $table->integer('number_of_seats');
            $table->string('blue_book_photo')->nullable();
            $table->decimal('car_price_per_km', 8, 2);
            $table->decimal('car_price_per_day', 8, 2);
            $table->string('car_photo')->nullable();
            $table->string('available')->default('no');
            $table->string('driver_name')->nullable();
            $table->string('driver_number')->nullable();
            $table->string('driver_photo')->nullable();
            $table->string('driving_experience')->nullable();
            $table->string('licence_photo')->nullable();
            $table->unsignedBigInteger('owner_id');
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
