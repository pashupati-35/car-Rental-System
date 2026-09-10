<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'customer';

    protected $fillable = [
        'name',
        'phone_number',
        'address',
        'gender',
        'email',
        'password',
        'admin_id',
        'owner_id',
        'is_mfa_enabled',
        'is_email_authentication_enabled',
        'mfa_secret_code',
        'mfa_authentication_image',
        'is_active',
        'is_login_verified',
        'last_logged_in',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_mfa_enabled' => 'boolean',
            'is_email_authentication_enabled' => 'boolean',
            'is_active' => 'boolean',
            'is_login_verified' => 'boolean',
            'last_logged_in' => 'datetime',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function bookings()
    {
        return $this->hasMany(BookingCar::class);
    }
}
