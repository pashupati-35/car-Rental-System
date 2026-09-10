<?php

namespace App\Models\Cms\VideoGallery;

use Cohensive\Embed\Facades\Embed;
use Cviebrock\EloquentSluggable\Sluggable;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoGallery extends Model
{
    use HasFactory, Loggable,Sluggable, SoftDeletes;

    //    protected $uploadPath = "album";
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    protected $fillable = [
        'id',
        'slug',
        'category_id',
        'title',
        'video_url',
        'short_description',
        'tags',
        'description',
        'position',
        'is_active',
        'is_featured',
    ];

    protected $appends = [
        'video_html',
    ];

    public function getVideoHtmlAttribute()
    {
        $embed = Embed::make($this->video_url)->parseUrl();

        if (! $embed) {
            return '';
        }

        $embed->setAttribute(['width' => '100%', 'height' => 'auto']);

        return $embed->getHtml();
    }

    public function getVideoTagsAttribute()
    {
        if (! empty($this->tags)) {

        }

        return $embed->getHtml();
    }

    public function category()
    {
        return $this->belongsTo(VideoGalleryCategory::class, 'category_id');
    }
}
