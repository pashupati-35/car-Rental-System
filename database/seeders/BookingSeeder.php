<?php

namespace Database\Seeders;

use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = Car::all();
        $customers = Customer::all();

        if ($cars->isEmpty() || $customers->isEmpty()) {
            return;
        }

        $routes = [
            ['pickup' => 'Tribhuvan International Airport (KTM)', 'drop' => 'Lakeside, Pokhara', 'days' => 4, 'price' => 480.00, 'status' => 'confirm', 'purpose' => 'vacation'],
            ['pickup' => 'Kathmandu City Center', 'drop' => 'Sauraha, Chitwan National Park', 'days' => 3, 'price' => 320.00, 'status' => 'confirm', 'purpose' => 'wildlife_tour'],
            ['pickup' => 'Lalitpur Heritage Area', 'drop' => 'Nagarkot Sunrise Point', 'days' => 2, 'price' => 190.00, 'status' => 'completed', 'purpose' => 'sightseeing'],
            ['pickup' => 'Baneshwor, Kathmandu', 'drop' => 'Dhulikhel Hill Station', 'days' => 1, 'price' => 110.00, 'status' => 'confirm', 'purpose' => 'business'],
            ['pickup' => 'Pokhara Airport', 'drop' => 'Annapurna Base Camp Trailhead (Nayapul)', 'days' => 5, 'price' => 600.00, 'status' => 'confirm', 'purpose' => 'trekking'],
            ['pickup' => 'Kathmandu', 'drop' => 'Lumbini Sacred Garden', 'days' => 4, 'price' => 520.00, 'status' => 'pending', 'purpose' => 'pilgrimage'],
            ['pickup' => 'Bhaktapur Durbar Square', 'drop' => 'Chandragiri Cable Car Station', 'days' => 1, 'price' => 95.00, 'status' => 'confirm', 'purpose' => 'family_trip'],
            ['pickup' => 'Patan Durbar Square', 'drop' => 'Bandipur Heritage Village', 'days' => 3, 'price' => 380.00, 'status' => 'confirm', 'purpose' => 'holiday'],
            ['pickup' => 'Tribhuvan International Airport', 'drop' => 'Thamel Hotel Hub', 'days' => 1, 'price' => 75.00, 'status' => 'completed', 'purpose' => 'airport_transfer'],
            ['pickup' => 'Kathmandu', 'drop' => 'Jiri - Gateway to Everest', 'days' => 6, 'price' => 850.00, 'status' => 'confirm', 'purpose' => 'expedition'],
            ['pickup' => 'Pokhara Lakeside', 'drop' => 'Sarangkot Paragliding Point', 'days' => 1, 'price' => 80.00, 'status' => 'confirm', 'purpose' => 'adventure'],
            ['pickup' => 'Butwal Bus Terminal', 'drop' => 'Palpa Tansen Historic Town', 'days' => 2, 'price' => 220.00, 'status' => 'confirm', 'purpose' => 'tourism'],
            ['pickup' => 'Chitwan', 'drop' => 'Janakpurdham Temple Complex', 'days' => 3, 'price' => 390.00, 'status' => 'pending', 'purpose' => 'cultural_tour'],
            ['pickup' => 'Kathmandu', 'drop' => 'Kande / Australian Camp Base', 'days' => 4, 'price' => 510.00, 'status' => 'confirm', 'purpose' => 'trekking'],
            ['pickup' => 'Lalitpur', 'drop' => 'Gorkha Durbar Historic Palace', 'days' => 2, 'price' => 270.00, 'status' => 'confirm', 'purpose' => 'heritage_visit'],
            ['pickup' => 'Nepalgunj Airport', 'drop' => 'Bardiya National Park Safari', 'days' => 5, 'price' => 700.00, 'status' => 'confirm', 'purpose' => 'safari'],
            ['pickup' => 'Biratnagar', 'drop' => 'Ilam Tea Garden Hills', 'days' => 3, 'price' => 420.00, 'status' => 'pending', 'purpose' => 'leisure'],
            ['pickup' => 'Kathmandu', 'drop' => 'Shivapuri National Park Gate', 'days' => 1, 'price' => 85.00, 'status' => 'completed', 'purpose' => 'day_hiking'],
            ['pickup' => 'Dhangadhi', 'drop' => 'Shuklaphanta Wildlife Reserve', 'days' => 4, 'price' => 540.00, 'status' => 'confirm', 'purpose' => 'wildlife_photography'],
            ['pickup' => 'Pokhara', 'drop' => 'Begnas Lake & Peace Pagoda', 'days' => 2, 'price' => 210.00, 'status' => 'confirm', 'purpose' => 'weekend_getaway'],
        ];

        foreach ($routes as $index => $r) {
            $car = $cars[$index % $cars->count()];
            $customer = $customers[$index % $customers->count()];

            $pickupDate = Carbon::now()->addDays($index - 5)->format('Y-m-d');
            $dropDate = Carbon::now()->addDays($index - 5 + $r['days'])->format('Y-m-d');

            BookingCar::updateOrCreate(
                [
                    'car_id' => $car->id,
                    'customer_id' => $customer->id,
                    'pick_up_date' => $pickupDate,
                ],
                [
                    'pickup_location' => $r['pickup'],
                    'drop_location' => $r['drop'],
                    'last_date' => $dropDate,
                    'total_price' => $r['price'],
                    'status' => $r['status'],
                    'purpose' => $r['purpose'],
                ]
            );
        }
    }
}
