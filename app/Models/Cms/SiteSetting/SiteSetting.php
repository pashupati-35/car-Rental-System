<?php

namespace App\Models\Cms\SiteSetting;

use App\Http\Traits\Loggable;
use App\Observers\Cms\SiteSetting\SiteSettingObserver;
use App\Services\Traits\UploadPathTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(SiteSettingObserver::class)]
class SiteSetting extends Model
{
    use HasFactory, Loggable, SoftDeletes, UploadPathTrait;

    public $uploadPath = 'setting';

    protected $fillable = [
        'description',
        'mobile',
        'phone',
        'email',
        'support_email',
        'website',
        'address',
        'whatsapp',
        'viber',
        'pininterest',
        'tiktok',
        'linkedin',
        'instagram',
        'youtube',
        'twitter',
        'facebook',
        'copy_right_text',
        'cookie_content_text',
        'terms_condition',
        'zoom_link',
        'map_url',
        'tagline',
        'logo',
        'email_logo_image',
        'app_logo',
        'enable_cookies',
        'fav_icon',
        'facebook_chat_widgets',
        'google_analytics',
        'pixels',
        'slogan',
        'login_bg_image',
        'login_bg_color',
        'primary_color',
        'secondary_color',
        'storage_type',
        'storage_endpoint',
        'storage_access_key',
        'storage_secret_key',
        'storage_region',
        'storage_bucket_name',
        'storage_url',
        'colors_variables',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_user_name',
        'mail_password',
        'mail_encryption',
        'mail_sender_name',
        'mail_sender_address',
        'date_format',
        'tax_percentage',
        'pan_no',
        'vat_no',
        'company_name',
        'address_type',
        'footer_logo',
        'is_admission_form_active',
        'seo_title',
        'seo_keyword',
        'seo_description',
        'display_storage',
        'display_smtp',
    ];

    protected $appends = [
        'fav_icon_path', 'app_logo_path', 'logo_path', 'login_bg_path', 'footer_logo_path', 'fb_chat_json_values', 'email_logo_path',
    ];

    /**
     * Resolve one of the uploaded image columns to its `['original' => …, 'thumb' => …]`
     * URLs. Returns an empty array when nothing has been uploaded for that column.
     */
    protected function resolveImagePath(?string $fileName): array
    {
        if (empty($fileName)) {
            return [];
        }

        return getImagePath($this->getUploadPath($this->uploadPath), $fileName) ?? [];
    }

    public function getFavIconPathAttribute()
    {
        return $this->resolveImagePath($this->fav_icon);
    }

    public function getFbChatJsonValuesAttribute()
    {
        return json_decode($this->facebook_chat_widgets ?? '', true);
    }

    public function getColorsVariablesJsonValuesAttribute()
    {
        return json_decode($this->colors_variables ?? '', true);
    }

    public function getLogoPathAttribute()
    {
        return $this->resolveImagePath($this->logo);
    }

    public function getEmailLogoPathAttribute()
    {
        return $this->resolveImagePath($this->email_logo_image);
    }

    public function getFooterLogoPathAttribute()
    {
        return $this->resolveImagePath($this->footer_logo);
    }

    public function getAppLogoPathAttribute()
    {
        return $this->resolveImagePath($this->app_logo);
    }

    public function getLoginBgPathAttribute()
    {
        return $this->resolveImagePath($this->login_bg_image);
    }
}
