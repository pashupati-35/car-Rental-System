<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_timezone', function (Blueprint $table) {
            $table->id();
            $table->morphs('user');
            $table->string('timezone')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_type', 'user_id']);
        });

        $now = now();

        foreach (
            [
                'admins'    => 'admin',
                'owners'    => 'owner',
                'customers' => 'customer',
            ] as $table => $type
        ) {
            if (Schema::hasTable($table)) {
                DB::table($table)
                    ->select('id')
                    ->orderBy('id')
                    ->chunkById(500, function ($users) use ($type, $now) {
                        $data = $users->map(fn($user) => [
                            'user_type'  => $type,
                            'user_id'    => $user->id,
                            'timezone'   => 'Asia/Kathmandu',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ])->toArray();

                        if (! empty($data)) {
                            DB::table('user_timezone')->insert($data);
                        }
                    });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_timezone');
    }
};
