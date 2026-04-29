<?php

namespace Database\Seeders;

use App\Models\Admin;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
//
       Admin::create([
           'name' => 'pashupati sah',
           'email' => 'pashupatisah@gmail.com',
           'password' => bcrypt('password'),
       ]);
    }
}
