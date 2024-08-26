<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\BookingCar;

class DeleteOldBookings extends Command
{
    protected $signature = 'bookings:cleanup';
    protected $description = 'Delete bookings that are more than 3 days old or have a status of "cancel"';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $dateThreshold = Carbon::now()->subDays(3);

        $query = BookingCar::where('created_at', '<', $dateThreshold)
            ->orWhere('status', 'cancel');

        $count = $query->count();

        $query->delete();

        $this->info("Old and canceled bookings deleted successfully. Total deleted: $count");
    }
}
