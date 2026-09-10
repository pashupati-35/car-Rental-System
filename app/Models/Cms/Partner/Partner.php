<?php

namespace App\Models\Cms\Partner;

use App\Http\Traits\Loggable;
use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, Loggable, Sluggable,  SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'our-partners';

    protected $fillable = ['title', 'slug', 'description', 'url', 'featured_photo', 'is_active'];

    protected $appends = ['featured_photo_path'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    public function getFeaturedPhotoPathAttribute()
    {
        $featuredPhotoPath = [];

        if (! empty($this->featured_photo)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $featuredPhotoPath = getImagePath($uploadPath, $this->featured_photo);
        }

        return $featuredPhotoPath;
    }
}
