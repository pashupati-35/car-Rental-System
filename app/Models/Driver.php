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

    protected $appends = ['image_path', 'image', 'photo_path', 'license_photo_path', 'license_photo_url', 'file_path'];

    public function getImagePathAttribute()
    {
        $img = $this->photo ?? $this->image ?? null;
        if (! empty($img)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);

            return getImagePath($uploadPath, $img);
        }

        return null;
    }

    public function getPhotoPathAttribute()
    {
        return $this->image_path;
    }

    public function getImageAttribute()
    {
        if (isset($this->image_path['original'])) {
            return $this->image_path['original'];
        }
        if (! empty($this->photo)) {
            return asset(ltrim($this->photo, '/'));
        }

        return null;
    }

    public function getLicensePhotoPathAttribute()
    {
        $doc = $this->license_photo ?? null;
        if (! empty($doc)) {
            return getFilePath('uploads/drivers/license', $doc);
        }

        return null;
    }

    public function getLicensePhotoUrlAttribute()
    {
        if (isset($this->license_photo_path['original'])) {
            return $this->license_photo_path['original'];
        }
        $doc = $this->license_photo ?? null;
        if (! empty($doc)) {
            return asset(ltrim($doc, '/'));
        }

        return null;
    }

    public function getFilePathAttribute()
    {
        return $this->license_photo_path ?? $this->image_path;
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
