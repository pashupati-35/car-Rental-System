<?php

namespace App\Models\Cms\Media;

use App\Services\Traits\UploadPathTrait;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, Loggable, SoftDeletes, UploadPathTrait;

    protected $table = 'medias';

    protected $uploadPath = 'media';

    protected $fillable = [
        'image',
        'title',
        'type',
        'size',
        'is_downloadable',
        'is_featured',
        'uploaded_by',
        'is_active',

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
