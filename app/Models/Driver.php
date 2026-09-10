<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';

    protected $fillable = [
        'owner_id',
        'name',
        'phone',
        'email',
        'license_number',
        'experience_years',
        'photo',
        'license_photo',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'driver_id');
    }
}
