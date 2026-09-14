<?php

namespace App\Models\Cms\Enquiry;

use App\Services\Traits\UploadPathTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use HasFactory, Sluggable, SoftDeletes, UploadPathTrait;

    protected $table = 'enquiries';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'subject',
        'message',
        'phone',
        'token',
        'mark_as_read',
    ];

    protected $casts = [
        'mark_as_read' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true,
            ],
        ];
    }
}
