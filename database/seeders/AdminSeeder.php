<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Pashupati Sah',
                'first_name' => 'Pashupati',
                'middle_name' => '',
                'last_name' => 'Sah',
                'username' => 'pashupati35',
                'email' => 'pashupatisah35@gmail.com',
                'password' => Hash::make('password'),
                'contact_number' => '+977-9841234567',
                'mobile' => '+977-9841234567',
                'phone' => '+977-1-4455667',
                'address' => 'Kathmandu, Bagmati, Nepal',
                'designation' => 'Super Administrator & Fleet Director',
                'position' => 'Chief Executive',
                'gender' => 'male',
                'marital_status' => 'single',
                'nationality' => 'Nepali',
                'citizenship_number' => '27-01-76-12345',
                'passport_number' => 'N1234567',
                'user_type' => 'super_admin',
                'access_type' => 'full_access',
                'has_email_access' => true,
                'approval_status' => 'approved',
                'is_submitted' => true,
                'theme_style' => 'light',
                'unique_identifier' => 'ADM-0001',
            ],
        ];

        foreach ($admins as $data) {
            Admin::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
