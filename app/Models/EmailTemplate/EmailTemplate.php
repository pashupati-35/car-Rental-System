<?php

namespace App\Models\EmailTemplate;

use App\Http\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory, Loggable, SoftDeletes;

    protected $fillable = [
        'title',
        'identifier',
        'subject',
        'role',
        'type',
        'description',
        'message_content',
        'accepted_inputs',
        'message_data',
        'info_message',
        'alert_message',
        'cta_url',
        'cta_text',
        'secondary_cta_url',
        'secondary_cta_text',
        'is_active',
    ];
}
