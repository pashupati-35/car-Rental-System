<?php

namespace App\Models\Cms\Page;

use Cviebrock\EloquentSluggable\Sluggable;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, Loggable, Sluggable, SoftDeletes;

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
        'custom_slug',
        'content',
        'position',
        'seo_title',
        'seo_keyword',
        'seo_description',
        'views',
        'is_deleted',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
