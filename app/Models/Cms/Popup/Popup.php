<?php

namespace App\Models\Cms\Popup;

use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Popup extends Model
{
    use HasFactory, Loggable, Sluggable, SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'popup';

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
            ],
        ];
    }

    protected $fillable = [
        'title',
        'slug',
        'description',
        'link',
        'type',
        'video_url',
        'location',
        'show_location',
        'image',
        'start_date',
        'end_date',
        'is_active',
        'position',

    ];

    protected $appends = [
        'image_path',
    ];

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
