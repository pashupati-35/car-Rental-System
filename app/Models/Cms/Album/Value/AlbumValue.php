<?php

namespace App\Models\Cms\Album\Value;

use App\Http\Traits\Loggable;
use App\Models\Cms\Album\Album;
use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlbumValue extends Model
{
    use HasFactory, Loggable, Sluggable, SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'album/value';

    protected $fillable = [
        'album_id',
        'title',
        'path',
        'is_featured',
        'position',
        'slug',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
            ],
        ];
    }

    protected $appends = [
        'image_path',
    ];

    public function getImagePathAttribute()
    {
        $imagePath = [];
        if (! empty($this->path)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $imagePath = getImagePath($uploadPath, $this->path);
        }

        return $imagePath;
    }

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }
}
