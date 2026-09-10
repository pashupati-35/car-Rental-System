<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Owner extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $guard = 'owner';

    protected $fillable = [
        'full_name',
        'contact_number',
        'address',
        'gender',
        'email',
        'password',
        'admin_id',
        'is_mfa_enabled',
        'mfa_secret_code',
        'mfa_authentication_image',
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
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
}
