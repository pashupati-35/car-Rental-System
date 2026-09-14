<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Driver;
use App\Models\Owner;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = Owner::all();
        $drivers = Driver::all();

        $defaultOwnerId = $owners->first() ? $owners->first()->id : 1;
        $defaultDriverId = $drivers->first() ? $drivers->first()->id : 1;

        $cars = [
            ['name' => 'Toyota', 'model' => 'Fortuner 4x4 Premium', 'number' => 'BA-1-PA-1024', 'seats' => 7, 'km_price' => 25.00, 'day_price' => 150.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Hyundai', 'model' => 'Creta SX (O) Turbo', 'number' => 'BA-2-CHA-2048', 'seats' => 5, 'km_price' => 18.00, 'day_price' => 85.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Mahindra', 'model' => 'Scorpio-N Z8L 4WD', 'number' => 'BA-3-PA-3096', 'seats' => 7, 'km_price' => 20.00, 'day_price' => 110.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Suzuki', 'model' => 'Grand Vitara Hybrid', 'number' => 'BA-4-CHA-4120', 'seats' => 5, 'km_price' => 16.00, 'day_price' => 75.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Toyota', 'model' => 'Hilux Adventure 2.8L', 'number' => 'BA-5-PA-5210', 'seats' => 5, 'km_price' => 28.00, 'day_price' => 165.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Kia', 'model' => 'Seltos GT Line', 'number' => 'BA-6-CHA-6311', 'seats' => 5, 'km_price' => 19.00, 'day_price' => 90.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Ford', 'model' => 'Endeavour 4x4 Titanium', 'number' => 'BA-7-PA-7422', 'seats' => 7, 'km_price' => 26.00, 'day_price' => 160.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Tata', 'model' => 'Harrier Dark Edition', 'number' => 'BA-8-CHA-8533', 'seats' => 5, 'km_price' => 19.50, 'day_price' => 95.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Hyundai', 'model' => 'Tucson AWD Signature', 'number' => 'BA-9-PA-9644', 'seats' => 5, 'km_price' => 22.00, 'day_price' => 125.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Nissan', 'model' => 'Navara PRO-4X', 'number' => 'BA-10-CHA-1055', 'seats' => 5, 'km_price' => 27.00, 'day_price' => 155.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Toyota', 'model' => 'Innova Crysta 2.4 ZX', 'number' => 'BA-11-PA-2166', 'seats' => 8, 'km_price' => 22.00, 'day_price' => 130.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Volkswagen', 'model' => 'Taigun GT Plus', 'number' => 'BA-12-CHA-3277', 'seats' => 5, 'km_price' => 17.50, 'day_price' => 80.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Skoda', 'model' => 'Kushaq Style AT', 'number' => 'BA-13-PA-4388', 'seats' => 5, 'km_price' => 18.00, 'day_price' => 82.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Mahindra', 'model' => 'Thar LX 4x4 Hard Top', 'number' => 'BA-14-CHA-5499', 'seats' => 4, 'km_price' => 21.00, 'day_price' => 105.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Jeep', 'model' => 'Compass Limited 4x4', 'number' => 'BA-15-PA-6510', 'seats' => 5, 'km_price' => 24.00, 'day_price' => 140.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Honda', 'model' => 'City ZX CVT Sedan', 'number' => 'BA-16-CHA-7621', 'seats' => 5, 'km_price' => 16.00, 'day_price' => 70.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Toyota', 'model' => 'Land Cruiser Prado VX', 'number' => 'BA-17-PA-8732', 'seats' => 7, 'km_price' => 38.00, 'day_price' => 250.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'Kia', 'model' => 'Carnival Limousine 7S', 'number' => 'BA-18-CHA-9843', 'seats' => 7, 'km_price' => 30.00, 'day_price' => 180.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'MG', 'model' => 'Hector Plus 6-Seater', 'number' => 'BA-19-PA-0954', 'seats' => 6, 'km_price' => 20.00, 'day_price' => 100.00, 'status' => 'verified', 'available' => 'yes'],
            ['name' => 'BYD', 'model' => 'Atto 3 Electric SUV', 'number' => 'BA-20-CHA-1165', 'seats' => 5, 'km_price' => 15.00, 'day_price' => 95.00, 'status' => 'verified', 'available' => 'yes'],
        ];

        foreach ($cars as $index => $c) {
            $owner = isset($owners[$index % count($owners)]) ? $owners[$index % count($owners)] : null;
            $driver = isset($drivers[$index % count($drivers)]) ? $drivers[$index % count($drivers)] : null;

            Car::updateOrCreate(
                ['car_number' => $c['number']],
                [
                    'car_name' => $c['name'],
                    'car_model' => $c['model'],
                    'number_of_seats' => $c['seats'],
                    'car_price_per_km' => $c['km_price'],
                    'car_price_per_day' => $c['day_price'],
                    'available' => $c['available'],
                    'driver_name' => $driver ? $driver->name : 'Platform Driver',
                    'driver_number' => $driver ? $driver->phone : '+977-9800000000',
                    'driving_experience' => $driver ? $driver->experience_years : '5',
                    'owner_id' => $owner ? $owner->id : $defaultOwnerId,
                    'driver_id' => $driver ? $driver->id : $defaultDriverId,
                    'status' => $c['status'],
                ]
            );
        }
    }
}
