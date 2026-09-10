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
            $table->string('car_name')->nullable();
            $table->string('car_model')->nullable();
            $table->string('car_number')->nullable()->unique();
            $table->integer('number_of_seats')->nullable();
            $table->string('blue_book_photo')->nullable();
            $table->decimal('car_price_per_km', 8, 2)->nullable();
            $table->decimal('car_price_per_day', 8, 2)->nullable();
            $table->string('car_photo')->nullable();
            $table->string('available')->nullable()->default('no');
            $table->string('driver_name')->nullable();
            $table->string('driver_number')->nullable();
            $table->string('driver_photo')->nullable();
            $table->string('driving_experience')->nullable();
            $table->string('licence_photo')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->string('status')->nullable()->default('pending');
            $table->timestamps();
            $table->softDeletes()->nullable();
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
