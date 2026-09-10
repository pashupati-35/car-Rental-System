<?php

namespace App\Models;

use App\Services\Traits\UploadPathTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes, UploadPathTrait;

    protected $table = 'drivers';

    protected $uploadPath = 'driver';

    protected $fillable = [
        'owner_id',
        'name',
        'phone',
        'email',
        'license_number',
        'experience_years',
        'photo',
        'license_photo',
        'status',
    ];

    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        $img = $this->photo ?? null;
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

    public function cars()
    {
        return $this->hasMany(Car::class, 'driver_id');
    }
}
