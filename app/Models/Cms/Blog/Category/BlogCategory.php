<?php

namespace App\Models\Cms\Blog\Category;

use App\Http\Traits\Loggable;
use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategory extends Model
{
    use HasFactory, Loggable, Sluggable, SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'blog/category';

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    protected $fillable = [
        'title',
        'slug',
        'description',
        'featured_image',
        'is_active',
    ];

    protected $appends = ['featured_image_path'];

    public function getFeaturedImagePathAttribute()
    {
        $imagePath = [];
        if (! empty($this->featured_image)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $imagePath = getImagePath($uploadPath, $this->featured_image);
        }

        return $imagePath;
    }
}
