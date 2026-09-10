<?php

namespace App\Models\Cms\Service;

use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Services extends Model
{
    use HasFactory, Sluggable, SoftDeletes , UploadPathTrait;

    protected $table = 'services';

    protected $uploadPath = 'services';

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
        'description',
        'image',
        'type',
        'is_active',
        'position',
        'price',
        'seo_title',
        'seo_keyword',
        'seo_description',
        'social_share_image',
    ];

    protected $appends = [
        'image_path',
        'social_share_image_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getImagePathAttribute()
    {
        if (! empty($this->image)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $imagePath = getImagePath($uploadPath, $this->image);
        }

        return $imagePath ?? null;
    }

    public function getSocialShareImagePathAttribute()
    {
        if (! empty($this->social_share_image)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $imagePath = getImagePath($uploadPath, $this->social_share_image);
        }

        return $imagePath ?? null;
    }
}
