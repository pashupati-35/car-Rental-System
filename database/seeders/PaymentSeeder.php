<?php

namespace Database\Seeders;

use App\Models\BookingCar;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = BookingCar::with(['customer', 'car'])->get();

        if ($bookings->isEmpty()) {
            return;
        }

        foreach ($bookings->take(20) as $index => $b) {
            $lastDigits = str_pad(1111 + ($index * 333) % 8888, 4, '0', STR_PAD_LEFT);
            $month = str_pad(($index % 12) + 1, 2, '0', STR_PAD_LEFT);
            $year = 26 + ($index % 5);

            Payment::updateOrCreate(
                ['booking_id' => $b->id],
                [
                    'customer_id' => $b->customer_id,
                    'car_id' => $b->car_id,
                    'amount' => $b->total_price ?? (100.00 + ($index * 25)),
                    'card_number' => '4532-xxxx-xxxx-'.$lastDigits,
                    'expiry_date' => $month.'/'.$year,
                    'cvv' => str_pad(100 + ($index * 17) % 899, 3, '0', STR_PAD_LEFT),
                ]
            );
        }
    }
}
