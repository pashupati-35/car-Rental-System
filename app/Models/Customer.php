<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard='customer';


    protected $fillable = [
        'name',
        'phone_number',
        'address',
        'gender',
        'email',
        'password',
        'admin_id',
        'owner_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Define relationship with Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // Define relationship with Owner
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
    public function bookings()
    {
        return $this->hasMany(BookingCar::class);
    }
}
