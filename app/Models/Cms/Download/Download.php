<?php

namespace App\Models\Cms\Download;

use App\Models\Cms\Download\Type\DownloadType;
use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Download extends Model
{
    use HasFactory, Loggable, Sluggable, SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'downloads';

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
        'preview_image',
        'description',
        'slug',
        'download_type_id',
        'position',
        'file',
        'type',
        'is_private',
        'public_hidden',
        'is_active',
    ];

    protected $appends = [
        'file_path', 'preview_image_path',
    ];

    public function getPreviewImagePathAttribute()
    {
        $imagePath = [];
        if (! empty($this->preview_image)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $imagePath = getImagePath($uploadPath, $this->preview_image);
        }

        return $imagePath;
    }

    public function getFilePathAttribute()
    {
        $path = null;
        if (! empty($this->file)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $path = getImagePath($uploadPath, $this->file);
        }

        return $path;
    }

    public function type()
    {
        return $this->belongsTo(DownloadType::class, 'download_type_id');
    }
}
