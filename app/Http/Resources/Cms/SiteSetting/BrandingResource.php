<?php

namespace App\Http\Resources\Cms\SiteSetting;

use App\Models\Cms\SiteSetting\SiteSetting;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public branding payload — the only part of the site settings that is exposed
 * without authentication, so the admin SPA can render its shell (logo, favicon,
 * login background, company identity) before anyone signs in.
 *
 * Everything here is safe to show to an anonymous visitor. Never add
 * credentials, storage keys or SMTP fields to this resource.
 *
 * @mixin SiteSetting
 */
class BrandingResource extends JsonResource
{
    /**
     * ℹ️ The admin consumes this payload flat; wrapping it in `data` would be a
     * breaking change for every existing client.
     */
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            // Image URLs fall back to the bundled defaults so the UI is never blank.
            'logo' => getLogo(),
            'favicon' => getFavIcon(),
            'app_logo' => getAppLogo(),
            'footer_logo' => getFooterLogo(),

            // ℹ️ Sourced from the model's `login_bg_path` accessor; exposed as a flat
            // URL to match the rest of this payload. Null when nothing is uploaded.
            'login_bg' => getLoginBackground(),
            'login_bg_color' => $this->login_bg_color,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,

            'company_name' => $this->company_name,
            'slogan' => $this->slogan,
            'tagline' => $this->tagline,
        ];
    }
}
