<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTimezone extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_timezone';

    protected $fillable = [
        'user_type',
        'user_id',
        'timezone',
    ];

    /**
     * Polymorphic relation to the user (Admin, Owner, Customer).
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}
