<?php

namespace App\Console\Commands;

use App\Models\BookingCar;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckReservationTimeout extends Command
{
    protected $signature = 'reservations:check-timeout';
    protected $description = 'Check for reservations that have timed out and update their status';

    public function handle()
    {
        try {
            // Ensure timezone is set to Asia/Kathmandu
            Carbon::setLocale('Asia/kathmandu');
            $timeout = Carbon::now()->subHours(2);
            $this->info('Timeout calculated as: ' . $timeout);

            // Log the query to debug
            DB::listen(function ($query) {
                Log::info('Executed Query: '.$query->sql, $query->bindings);
            });

            // Fetch records that need to be updated
            $records = BookingCar::where('status', 'reserved')
                ->where('created_at', '<=', $timeout)
                ->get();

            $this->info('Number of records found: ' . $records->count());

            // Log records before update
            foreach ($records as $record) {
                $this->info('ID: ' . $record->id . ', Created At: ' . $record->created_at);
            }

            // Check if the records are correctly fetched
            if ($records->count() > 0) {
                // Perform update
                $updatedRows = BookingCar::where('status', 'reserved')
                    ->where('created_at', '<=', $timeout)
                    ->update(['status' => 'cancel']);

                if ($updatedRows > 0) {
                    $this->info("Updated $updatedRows reservations.");
                } else {
                    $this->info('No reservations were updated.');
                }
            } else {
                $this->info('No records found to update.');
            }
        } catch (Exception $e) {
            Log::error('Error checking reservation timeouts: ' . $e->getMessage());
            $this->error('An error occurred while checking reservations.');
        }
    }
}
