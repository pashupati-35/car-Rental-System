<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Pashupati Sah', 'email' => 'pashupatisah35@gmail.com'],
            ['name' => 'Pashupati Sah (Secondary)', 'email' => 'pashupatisah@gmail.com'],
            ['name' => 'Demo User 1', 'email' => 'user1@example.com'],
            ['name' => 'Demo User 2', 'email' => 'user2@example.com'],
            ['name' => 'Demo User 3', 'email' => 'user3@example.com'],
            ['name' => 'Demo User 4', 'email' => 'user4@example.com'],
            ['name' => 'Demo User 5', 'email' => 'user5@example.com'],
            ['name' => 'Demo User 6', 'email' => 'user6@example.com'],
            ['name' => 'Demo User 7', 'email' => 'user7@example.com'],
            ['name' => 'Demo User 8', 'email' => 'user8@example.com'],
            ['name' => 'Demo User 9', 'email' => 'user9@example.com'],
            ['name' => 'Demo User 10', 'email' => 'user10@example.com'],
            ['name' => 'Demo User 11', 'email' => 'user11@example.com'],
            ['name' => 'Demo User 12', 'email' => 'user12@example.com'],
            ['name' => 'Demo User 13', 'email' => 'user13@example.com'],
            ['name' => 'Demo User 14', 'email' => 'user14@example.com'],
            ['name' => 'Demo User 15', 'email' => 'user15@example.com'],
            ['name' => 'Demo User 16', 'email' => 'user16@example.com'],
            ['name' => 'Demo User 17', 'email' => 'user17@example.com'],
            ['name' => 'Demo User 18', 'email' => 'user18@example.com'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
