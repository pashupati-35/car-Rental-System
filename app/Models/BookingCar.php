<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingCar extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $casts = [
        'pick_up_date' => 'datetime',
        'last_date' => 'datetime',
        'total_price' => 'float',
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

    /**
     * Get the payment associated with the booking.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id');
    }

    /**
     * Get all payments associated with the booking.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }
}
