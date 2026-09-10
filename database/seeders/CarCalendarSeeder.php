<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarCalendar;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CarCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = Car::all();
        if ($cars->isEmpty()) {
            return;
        }

        $statuses = ['available', 'reserved', 'booked'];

        for ($i = 0; $i < 20; $i++) {
            $car = $cars[$i % $cars->count()];
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $status = $statuses[$i % count($statuses)];

            CarCalendar::updateOrCreate(
                [
                    'car_id' => $car->id,
                    'date' => $date,
                ],
                [
                    'status' => $status,
                ]
            );
        }
    }
}
