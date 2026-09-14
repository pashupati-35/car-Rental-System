<?php

namespace App\Models\Cms\NewsAndUpdates;

use App\Http\Traits\Loggable;
use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsAndUpdates extends Model
{
    use HasFactory ,Loggable, Sluggable,  SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'news-and-updates';

    protected $fillable = ['title', 'slug', 'url', 'published_by', 'publish_date', 'social_share_image', 'is_active'];

    protected $appends = ['social_share_image_path'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
            ],
        ];
    }

    public function getSocialShareImagePathAttribute()
    {
        $socialShareImagePath = [];

        if (! empty($this->social_share_image)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $socialShareImagePath = getImagePath($uploadPath, $this->social_share_image);
        }

        return $socialShareImagePath;
    }
}
