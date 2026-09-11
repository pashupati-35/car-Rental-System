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
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('license_number')->nullable();
            $table->string('experience_years')->nullable()->default('1');
            $table->string('photo')->nullable();
            $table->string('license_photo')->nullable();
            $table->string('status')->nullable()->default('active');
            $table->timestamps();
            $table->softDeletes()->nullable();

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
