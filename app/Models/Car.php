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

    protected $appends = [
        'image_path',
        'image',
        'car_photo_path',
        'blue_book_path',
        'blue_book_url',
        'driver_photo_path',
        'licence_photo_path',
        'file_path',
    ];

    public function getImagePathAttribute()
    {
        $img = $this->car_photo ?? null;
        if (! empty($img)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);

            return getImagePath($uploadPath, $img);
        }

        return null;
    }

    public function getCarPhotoPathAttribute()
    {
        return $this->image_path;
    }

    public function getImageAttribute()
    {
        if (isset($this->image_path['original'])) {
            return $this->image_path['original'];
        }
        if (! empty($this->car_photo)) {
            return asset(ltrim($this->car_photo, '/'));
        }

        return null;
    }

    public function getBlueBookPathAttribute()
    {
        $doc = $this->blue_book_photo ?? null;
        if (! empty($doc)) {
            return getFilePath('uploads/bluebooks', $doc);
        }

        return null;
    }

    public function getBlueBookUrlAttribute()
    {
        if (isset($this->blue_book_path['original'])) {
            return $this->blue_book_path['original'];
        }
        $doc = $this->blue_book_photo ?? null;
        if (! empty($doc)) {
            return asset(ltrim($doc, '/'));
        }

        return null;
    }

    public function getDriverPhotoPathAttribute()
    {
        $img = $this->driver_photo ?? null;
        if (! empty($img)) {
            return getImagePath('uploads/drivers', $img);
        }

        return null;
    }

    public function getLicencePhotoPathAttribute()
    {
        $img = $this->licence_photo ?? null;
        if (! empty($img)) {
            return getFilePath('uploads/drivers/license', $img);
        }

        return null;
    }

    public function getFilePathAttribute()
    {
        return $this->blue_book_path ?? $this->licence_photo_path ?? $this->image_path;
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
