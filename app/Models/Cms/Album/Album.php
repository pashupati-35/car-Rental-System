<?php

namespace App\Models\Cms\Album;

use App\Http\Traits\Loggable;
use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use HasFactory, Loggable, Sluggable, SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'album';

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
        'album_id',
        'title',
        'slug',
        'cover_image',
        'description',
        'event_date',
        'is_active',
        'tags',
        'position',
    ];

    protected $appends = [
        'cover_image_path',
    ];

    public function getCoverImagePathAttribute()
    {
        $imagePath = [];
        if (! empty($this->cover_image)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $imagePath = getImagePath($uploadPath, $this->cover_image);
        }

        return $imagePath;
    }
}
