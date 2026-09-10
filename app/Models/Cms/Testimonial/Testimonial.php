<?php

namespace App\Models\Cms\Testimonial;

use App\Services\Traits\UploadPathTrait;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasFactory, Loggable, SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'testimonial';

    protected $fillable = [
        'title',
        'name',
        'description',
        'type',
        'job_title',
        'image',
        'rating',
        'status',
        'position',
        'is_active',

    ];

    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        $imagePath = [];
        if (! empty($this->image)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $imagePath = getImagePath($uploadPath, $this->image);
        }

        return $imagePath;
    }
}
