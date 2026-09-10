<?php

namespace App\Models\Cms\Slider;

use App\Models\Cms\Slider\Type\SliderType;
use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use HasFactory, Loggable, Sluggable, SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'slider';

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
        'slider_type_id',
        'description',
        'image',
        'link',
        'position',
        'new_tab',
        'heading_text',
        'sub_heading_text',
        'button_text',
        'show_button',
        'is_active',

    ];

    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        $imagePath = [];
        if (! empty($this->image)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            $imagePath = getImagePath($uploadPath, $this->image);
        }

        return $imagePath;
    }

    public function type()
    {
        return $this->belongsTo(SliderType::class, 'slider_type_id');
    }
}
