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
        if (! Schema::hasTable('logs')) {
            Schema::create('logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('admin_user_id')->nullable();
                $table->unsignedBigInteger('agent_id')->nullable();
                $table->dateTime('log_date')->nullable();
                $table->string('table_name', 100)->nullable();
                $table->unsignedBigInteger('table_id')->nullable();
                $table->string('log_type', 50)->nullable();
                $table->longText('data')->nullable();
                $table->longText('before_data')->nullable();
                $table->longText('after_data')->nullable();
                $table->timestamps();
                $table->softDeletes()->nullable();

                $table->index('user_id');
                $table->index('admin_user_id');
                $table->index('log_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
