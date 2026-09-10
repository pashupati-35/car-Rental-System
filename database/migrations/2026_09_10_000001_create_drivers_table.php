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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('license_number');
            $table->string('experience_years')->default('1');
            $table->string('photo')->nullable();
            $table->string('license_photo')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_trip'])->default('active');
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->nullable()->after('owner_id');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });

        Schema::dropIfExists('drivers');
    }
};
