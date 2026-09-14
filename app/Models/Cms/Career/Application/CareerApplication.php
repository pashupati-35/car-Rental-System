<?php

namespace App\Models\Cms\Career\Application;

use App\Models\Cms\Career\Career;
use App\Services\Traits\UploadPathTrait;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareerApplication extends Model
{
    use HasFactory, Loggable, SoftDeletes, UploadPathTrait;

    protected $uploadPath = 'career';

    protected $fillable = [
        'career_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'file',
        'received_at',
        'is_read',
        'is_shortlisted',
    ];

    protected $appends = ['file_path'];

    public function getFilePathAttribute()
    {
        if ($this->file) {
            $uploadPath = $this->getUploadPath($this->uploadPath);

            return getImagePath($uploadPath, $this->file);
        }

        return null;
    }

    public function career()
    {
        return $this->belongsTo(Career::class, 'career_id');
    }
}
