<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
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

        $admin = Admin::firstOrCreate(
            ['email' => 'pashupatisah@gmail.com'],
            [
                'name' => 'Pashupati Sah',
                'password' => Hash::make('password'),
            ]
        );

        $owner = Owner::firstOrCreate(
            ['email' => 'owner@futech.com'],
            [
                'full_name' => 'Rajesh Sharma',
                'contact_number' => '+977 9841234567',
                'address' => 'Kathmandu, Nepal',
                'gender' => 'male',
                'password' => Hash::make('password'),
                'admin_id' => $admin->id,
            ]
        );

        $customer = Customer::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Aayush Adhikari',
                'phone_number' => '+977 9812345678',
                'address' => 'Lalitpur, Nepal',
                'gender' => 'male',
                'password' => Hash::make('password'),
                'admin_id' => $admin->id,
                'owner_id' => $owner->id,
            ]
        );

        // Drivers in dedicated drivers table
        $driver1 = Driver::firstOrCreate(
            ['license_number' => 'DL-01-98762'],
            [
                'owner_id' => $owner->id,
                'name' => 'Bikram Thapa',
                'phone' => '+977 9801122334',
                'email' => 'bikram.driver@gmail.com',
                'experience_years' => '6',
                'status' => 'active',
            ]
        );

        $driver2 = Driver::firstOrCreate(
            ['license_number' => 'DL-02-54321'],
            [
                'owner_id' => $owner->id,
                'name' => 'Ramesh Chaudhary',
                'phone' => '+977 9809988776',
                'email' => 'ramesh.driver@gmail.com',
                'experience_years' => '4',
                'status' => 'active',
            ]
        );

        // Fleet Cars with driver_id link and owner
        Car::firstOrCreate(
            ['car_number' => 'BA-1-PA-1024'],
            [
                'car_name' => 'Toyota',
                'car_model' => 'Fortuner 4x4',
                'number_of_seats' => 7,
                'car_price_per_km' => 20.00,
                'car_price_per_day' => 120.00,
                'available' => 'yes',
                'driver_name' => $driver1->name,
                'driver_number' => $driver1->phone,
                'driving_experience' => $driver1->experience_years,
                'owner_id' => $owner->id,
                'driver_id' => $driver1->id,
                'status' => 'verified',
            ]
        );

        Car::firstOrCreate(
            ['car_number' => 'BA-2-CHA-5520'],
            [
                'car_name' => 'Hyundai',
                'car_model' => 'Creta SX Plus',
                'number_of_seats' => 5,
                'car_price_per_km' => 14.00,
                'car_price_per_day' => 65.00,
                'available' => 'yes',
                'driver_name' => $driver2->name,
                'driver_number' => $driver2->phone,
                'driving_experience' => $driver2->experience_years,
                'owner_id' => $owner->id,
                'driver_id' => $driver2->id,
                'status' => 'verified',
            ]
        );
    }
}
