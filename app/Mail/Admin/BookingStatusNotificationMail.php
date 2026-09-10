<?php

namespace App\Mail\Admin;

use App\Models\BookingCar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingStatusNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public BookingCar $booking;
    public string $status;
    public ?string $remarks;

    public function __construct(BookingCar $booking, string $status, ?string $remarks = null)
    {
        $this->booking = $booking;
        $this->status = $status;
        $this->remarks = $remarks;
    }

    public function build()
    {
        $carName = $this->booking->car ? "{$this->booking->car->car_name} {$this->booking->car->car_model}" : 'Vehicle';
        $subject = $this->status === 'confirm'
            ? "Rental Booking Confirmed #BK-{$this->booking->id} ({$carName})"
            : "Rental Booking Cancelled #BK-{$this->booking->id}";

        $fromAddress = config('mail.from.address') ?: 'admin@autorent.com';
        $fromName = config('mail.from.name') ?: 'AutoRent Booking Desk';

        return $this->view('emails.booking_status')
            ->subject($subject)
            ->from($fromAddress, $fromName);
    }
}
