<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with 20 records per table.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            OwnerSeeder::class,
            DriverSeeder::class,
            CarSeeder::class,
            CustomerSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
            CarCalendarSeeder::class,
            EmailTemplateSeeder::class,
            LogSeeder::class,
            CmsSeeder::class,
        ]);
    }
}
