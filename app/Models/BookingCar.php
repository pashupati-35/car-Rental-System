<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingCar extends Model
{
    use HasFactory;

    protected $table = 'booking_car';

    protected $fillable = [
        'pickup_location',
        'drop_location',
        'pick_up_date',
        'last_date',
        'total_price',
        'status',
        'car_id',
        'customer_id',
        'purpose',        
        'other_purpose', 
    ];

    /**
     * Get the car associated with the booking.
     */
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Get the customer who made the booking.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
