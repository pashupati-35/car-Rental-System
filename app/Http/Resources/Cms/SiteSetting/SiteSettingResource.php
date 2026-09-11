<?php

namespace App\Http\Resources\Cms\SiteSetting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $displaySmtpSetting = [
            ! empty($this->mail_driver),
            ! empty($this->mail_host),
            ! empty($this->mail_port),
            ! empty($this->mail_user_name),
            ! empty($this->mail_password),
            ! empty($this->mail_sender_name),
            ! empty($this->mail_sender_address),
        ];

        $resource = [
            'id' => $this->id,
            'phone' => $this->phone,
            'email' => $this->email,
            'support_email' => $this->support_email,
            'address' => $this->address,
            'mobile' => $this->mobile,
            'map_url' => $this->map_url,
            'zoom_link' => $this->zoom_link,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'youtube' => $this->youtube,
            'instagram' => $this->instagram,
            'linkedin' => $this->linkedin,
            'tiktok' => $this->tiktok,
            'pininterest' => $this->pininterest,
            'viber' => $this->viber,
            'whatsapp' => $this->whatsapp,
            'facebook_chat_widgets' => $this->facebook_chat_widgets,
            'google_analytics' => $this->google_analytics,
            'pixels' => $this->pixels,
            'slogan' => $this->slogan,
            'tagline' => $this->tagline,
            'website' => $this->website,
            'date_format' => $this->date_format,
            'address_type' => $this->address_type,
            'enable_cookies' => $this->enable_cookies,
            'cookie_content_text' => $this->cookie_content_text,
            'copy_right_text' => $this->copy_right_text,
            'fav_icon' => $this->fav_icon,
            'logo' => $this->logo,
            'login_bg_image' => $this->login_bg_image,
            'login_bg_color' => $this->login_bg_color,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'colors_variables' => $this->colors_variables,
            'app_logo' => $this->app_logo,
            'email_logo_image' => $this->email_logo_image,
            'footer_logo' => $this->footer_logo,
            'tax_percentage' => $this->tax_percentage,
            'company_name' => $this->company_name,
            'pan_no' => $this->pan_no,
            'vat_no' => $this->vat_no,
            'fav_icon_path' => $this->fav_icon_path,
            'app_logo_path' => $this->app_logo_path,
            'logo_path' => $this->logo_path,
            'login_bg_path' => $this->login_bg_path,
            'footer_logo_path' => $this->footer_logo_path,
            'display_storage' => $this->display_storage,
            'email_logo_path' => $this->email_logo_path,
            'display_smtp_setting' => in_array(false, $displaySmtpSetting) ? false : true,
            'is_admission_form_active' => $this->is_admission_form_active,
            'seo_title' => $this->seo_title,
            'seo_keyword' => $this->seo_keyword,
            'seo_description' => $this->seo_description,
            'display_smtp' => $this->display_smtp,

            /*
             * ℹ️ Configuration the admin form has to round-trip.
             *
             * These were previously omitted, which meant the SMTP, Storage and
             * Advanced tabs always loaded empty — and saving one of them posted
             * nulls straight over the stored credentials. Secrets are still never
             * echoed back; the `has_*` flags tell the UI one is on file so it can
             * offer "leave blank to keep the saved value" instead.
             */
            'mail_driver' => $this->mail_driver,
            'mail_host' => $this->mail_host,
            'mail_port' => $this->mail_port,
            'mail_user_name' => $this->mail_user_name,
            'mail_encryption' => $this->mail_encryption,
            'mail_sender_name' => $this->mail_sender_name,
            'mail_sender_address' => $this->mail_sender_address,
            'has_mail_password' => filled($this->mail_password),

            'storage_type' => $this->storage_type,
            'storage_endpoint' => $this->storage_endpoint,
            'storage_region' => $this->storage_region,
            'storage_bucket_name' => $this->storage_bucket_name,
            'storage_url' => $this->storage_url,
            'has_storage_access_key' => filled($this->storage_access_key),
            'has_storage_secret_key' => filled($this->storage_secret_key),

            'recaptcha_site_key' => $this->recaptcha_site_key,
            'has_recaptcha_secret_key' => filled($this->recaptcha_secret_key),
        ];

        return $resource;
    }
}
