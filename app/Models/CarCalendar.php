<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCalendar extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'car_calendar';

    // The attributes that are mass assignable.
    protected $fillable = [
        'car_id',
        'date',
        'status',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'date' => 'date',
    ];

    // Define the relationship to the Car model
    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
