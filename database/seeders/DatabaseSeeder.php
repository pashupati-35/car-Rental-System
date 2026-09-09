<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Owner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EmailTemplateSeeder::class,
        ]);

        if (!Admin::where('email', 'pashupatisah@gmail.com')->exists()) {
            Admin::create([
                'name' => 'Pashupati Sah',
                'email' => 'pashupatisah@gmail.com',
                'password' => Hash::make('password'),
                'is_active' => 1,
                'is_login_verified' => 1,
            ]);
        }
    }
}
