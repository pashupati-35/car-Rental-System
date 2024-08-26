<?php

namespace App\Console;

use App\Console\Commands\CheckReservationTimeout;
use App\Console\Commands\DeleteOldBookings;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Register your custom commands here
        CheckReservationTimeout::class,
        DeleteOldBookings::class,
    ];
    /**
     * Define the application's command schedule.
     */
//    protected function schedule(Schedule $schedule): void
//    {
//        // Schedule the reservation timeout check command to run every five minutes
////        $schedule->command('reservations:check-timeout')->everyFiveMinutes();
//        $schedule->command('reservations:check-timeout')->hourly(); // Adjust the frequency as needed
//
//    }
    protected function schedule(Schedule $schedule): void
    {
        // Schedule the reservation timeout check command to run every minute for testing
        $schedule->command('reservations:check-timeout')->everyMinute();
        $schedule->command('bookings:cleanup')->daily();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Automatically load all command classes in the Commands directory
        $this->load(__DIR__.'/Commands');

        // Register any additional console routes (commands) defined in routes/console.php
        require base_path('routes/console.php');
    }
}
