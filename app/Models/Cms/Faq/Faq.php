<?php

namespace App\Models\Cms\Faq;

use App\Models\Cms\Faq\Category\FaqCategory;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, Loggable, SoftDeletes;

    protected $fillable = [
        'title',
        'short_description',
        'tags',
        'description',
        'seo_title',
        'seo_description',
        'seo_keyword',
        'position',
        'is_active',
        'faq_category_id',
    ];

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }
}
