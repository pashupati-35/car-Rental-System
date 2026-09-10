<?php

namespace App\Models;

use App\Services\Traits\UploadPathTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes, UploadPathTrait;

    protected $table = 'cars';

    protected $uploadPath = 'cars';

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
        'driver_id',
        'status',
    ];

    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        $img = $this->car_photo ?? null;
        if (!empty($img)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            return getImagePath($uploadPath, $img);
        }
        return null;
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
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
