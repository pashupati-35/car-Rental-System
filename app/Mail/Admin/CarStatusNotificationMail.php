<?php

namespace App\Mail\Admin;

use App\Models\Car;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CarStatusNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Car $car;
    public string $status;
    public ?string $remarks;

    public function __construct(Car $car, string $status, ?string $remarks = null)
    {
        $this->car = $car;
        $this->status = $status;
        $this->remarks = $remarks;
    }

    public function build()
    {
        $subject = $this->status === 'verified'
            ? "Vehicle Listing Approved: {$this->car->car_name} ({$this->car->car_number})"
            : "Vehicle Listing Status Update: {$this->car->car_name} ({$this->car->car_number})";

        $fromAddress = config('mail.from.address') ?: 'admin@autorent.com';
        $fromName = config('mail.from.name') ?: 'AutoRent Fleet Operations';

        return $this->view('emails.car_status')
            ->subject($subject)
            ->from($fromAddress, $fromName);
    }
}
