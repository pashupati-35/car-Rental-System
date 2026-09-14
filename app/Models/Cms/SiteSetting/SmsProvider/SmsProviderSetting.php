<?php

namespace App\Models\Cms\SiteSetting\SmsProvider;

use App\Http\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsProviderSetting extends Model
{
    use HasFactory, Loggable, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'token',
        'sender',
        'is_active',
    ];
}
