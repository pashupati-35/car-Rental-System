<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $table = 'cars';


    protected $fillable = [
        'car_name',
        'car_model',
        'car_number',
        'number_of_seats',
        'blue_book_photo',
        'car_price_per_km',
        'car_price_per_day',
        'car_photo',
        'available',
        'driver_name',
        'driver_number',
        'driver_photo',
        'driving_experience',
        'licence_photo',
        'owner_id',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
    public function booking()
    {
        return $this->hasMany(BookingCar::class);
    }
    public function calendar()
    {
        return $this->hasMany(CarCalendar::class);
    }
}
