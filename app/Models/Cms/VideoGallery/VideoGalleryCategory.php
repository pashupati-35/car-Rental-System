<?php

namespace App\Models\Cms\VideoGallery;

use Cviebrock\EloquentSluggable\Sluggable;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoGalleryCategory extends Model
{
    use HasFactory, Loggable, Sluggable, SoftDeletes;

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
        'parent_id',
        'is_active',

    ];
}
