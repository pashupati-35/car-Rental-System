<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'car_id',
        'booking_id',
        'amount',
        'card_number',
        'expiry_date',
        'cvv',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    protected $hidden = [
        'cvv',
        'card_number',
    ];

    // Define relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function booking()
    {
        return $this->belongsTo(BookingCar::class);
    }
}
