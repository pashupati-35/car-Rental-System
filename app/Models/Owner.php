<?php

namespace App\Models;

use App\Services\Traits\UploadPathTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Owner extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, UploadPathTrait;
    
    protected $guard = 'owner';

    protected $uploadPath = 'owner';

    protected $fillable = [
        'unique_identifier',
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'contact_number',
        'mobile',
        'phone',
        'username',
        'address',
        'gender',
        'image',
        'email',
        'password',
        'admin_id',
        'date_of_birth',
        'marital_status',
        'nationality',
        'citizenship_number',
        'passport_number',
        'position',
        'designation',
        'user_type',
        'access_type',
        'has_email_access',
        'access_email_type',
        'approval_status',
        'register_type',
        'is_submitted',
        'theme_style',
        'emergency_contact',
        'contact_person_name',
        'contact_relationship',
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
        'mfa_authentication_image',
    ];

    protected $appends = ['full_name', 'image_path'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_mfa_enabled' => 'boolean',
            'is_email_authentication_enabled' => 'boolean',
            'is_active' => 'boolean',
            'is_login_verified' => 'boolean',
            'is_submitted' => 'boolean',
            'has_email_access' => 'boolean',
            'last_logged_in' => 'datetime',
            'date_of_birth' => 'date',
        ];
    }

    public function getFullNameAttribute()
    {
        if (!empty($this->first_name) || !empty($this->last_name)) {
            if (!empty($this->middle_name)) {
                return ucfirst(trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name));
            }
            return ucfirst(trim($this->first_name . ' ' . $this->last_name));
        }
        return $this->attributes['full_name'] ?? null;
    }

    public function getImagePathAttribute()
    {
        if (!empty($this->image)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);
            return getImagePath($uploadPath, $this->image);
        }
        return null;
    }

    protected static function booted(): void
    {
        static::creating(function (Owner $owner) {
            if (empty($owner->unique_identifier)) {
                $owner->unique_identifier = 'OWN-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
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
