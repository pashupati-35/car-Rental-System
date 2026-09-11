<?php

namespace App\Models\Cms\Faq\Category;

use App\Http\Traits\Loggable;
use App\Models\Cms\Faq\Faq;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqCategory extends Model
{
    use HasFactory, Loggable, Sluggable, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'position',
        'is_active',
        'old_table_id',
        'old_table_name',
    ];

    protected $appends = ['faqs'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function getFaqsAttribute()
    {
        $faqs = Faq::whereRaw('FIND_IN_SET(?,faq_category_id)', $this->id)->orderBy('position', 'ASC')->get();
        if (! empty($faqs)) {
            return $faqs;
        }

        return null;
    }
}
