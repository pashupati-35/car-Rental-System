<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function owners()
    {
        return $this->hasMany(Owner::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
