<?php

namespace App\Models\Cms\ContactUs;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends Model
{
    use HasFactory, Loggable, SoftDeletes;

    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'phone',
        'email',
        'subject',
        'message',
        'is_read',
        'replied',
        'is_active',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_active' => 'boolean',

    ];
}
