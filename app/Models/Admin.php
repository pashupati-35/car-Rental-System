<?php

namespace App\Models;

use App\Services\Traits\UploadPathTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, UploadPathTrait;

    protected $guard = 'admin';

    protected $uploadPath = 'admin';

    protected $fillable = [
        'unique_identifier',
        'first_name',
        'middle_name',
        'last_name',
        'name',
        'email',
        'contact_number',
        'mobile',
        'phone',
        'username',
        'address',
        'designation',
        'position',
        'avatar',
        'image',
        'date_of_birth',
        'gender',
        'marital_status',
        'nationality',
        'citizenship_number',
        'passport_number',
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
        'mfa_authentication_image',
    ];

    protected $appends = ['full_name', 'image_path', 'file_path'];

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
        if (! empty($this->first_name) || ! empty($this->last_name)) {
            if (! empty($this->middle_name)) {
                return ucfirst(trim($this->first_name.' '.$this->middle_name.' '.$this->last_name));
            }

            return ucfirst(trim($this->first_name.' '.$this->last_name));
        }

        return $this->attributes['name'] ?? null;
    }

    public function getImagePathAttribute()
    {
        $img = $this->image ?? $this->avatar ?? null;
        if (! empty($img)) {
            $uploadPath = $this->getUploadPath($this->uploadPath);

            return getImagePath($uploadPath, $img);
        }

        return null;
    }

    public function getFilePathAttribute()
    {
        return $this->image_path;
    }

    protected static function booted(): void
    {
        static::creating(function (Admin $admin) {
            if (empty($admin->unique_identifier)) {
                $admin->unique_identifier = 'ADM-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
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
