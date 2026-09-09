<?php

declare(strict_types=1);

use App\Models\AdminUser\AdminUser;
use App\Models\Cms\Menu\Menu;
use App\Models\Cms\SiteSetting\SiteSetting;
use App\Models\EmailTemplate\EmailTemplate;
use App\Repositories\Interfaces\Cms\SiteSetting\SiteSettingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nilambar\NepaliDate\NepaliDate;

if (! function_exists('formatDate')) {
    function formatDate(mixed $date, string $format = 'd M Y', mixed $default = null): mixed
    {
        return blank($date) ? $default : Carbon::parse($date)->format($format);
    }
}

if (! function_exists('formatMonthYear')) {
    function formatMonthYear(mixed $date, string $format = 'M Y', mixed $default = null): mixed
    {
        return formatDate($date, $format, $default);
    }
}

if (! function_exists('formatTime')) {
    function formatTime(mixed $time, string $format = 'g:i A'): ?string
    {
        if (blank($time)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i:s', (string) $time)->format($format);
        } catch (Throwable) {
            return Carbon::parse((string) $time)->format($format);
        }
    }
}

if (! function_exists('formatYearMonthDateTime')) {
    function formatYearMonthDateTime(mixed $date): ?string
    {
        return blank($date) ? null : Carbon::parse($date)->isoFormat('D MMM YYYY, hh:mm A');
    }
}

if (! function_exists('formatYearMonthDate')) {
    function formatYearMonthDate(mixed $date): ?string
    {
        return blank($date) ? null : Carbon::parse($date)->isoFormat('D MMM YYYY');
    }
}

if (! function_exists('getRandomString')) {
    function getRandomString(int $len): string
    {
        return Str::upper(Str::random(max(1, $len)));
    }
}

if (! function_exists('getDateString')) {
    function getDateString(mixed $date): ?string
    {
        return blank($date) ? null : Carbon::parse($date)->format('Y M d');
    }
}

if (! function_exists('getDateTimeString')) {
    function getDateTimeString(mixed $date): ?string
    {
        return blank($date) ? null : Carbon::parse($date)->format('Y M d h:i a');
    }
}

if (! function_exists('resolveBranchId')) {
    function resolveBranchId(?int $branchId): int
    {
        return $branchId ?? 1;
    }
}

if (! function_exists('appendZeroInNumber')) {
    function appendZeroInNumber(int|string $value, int $noOfDigit = 5): string
    {
        return str_pad((string) $value, $noOfDigit, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('getBase64ImageSize')) {
    function getBase64ImageSize(string $base64Image): float
    {
        $payload = Str::contains($base64Image, ',') ? Str::after($base64Image, ',') : $base64Image;
        $bytes = (int) floor(strlen(rtrim($payload, '=')) * 3 / 4);

        return round($bytes / 1024 / 1024, 4);
    }
}

if (! function_exists('formatPhone')) {
    function formatPhone(int|string|null $phone): ?string
    {
        if (blank($phone)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $phone) ?: '';

        return match (true) {
            strlen($digits) === 10 && str_starts_with($digits, '04') => substr($digits, 0, 4).' '.substr($digits, 4, 3).' '.substr($digits, 7),
            strlen($digits) >= 8 => substr($digits, 0, 2).' '.substr($digits, 2, 4).' '.substr($digits, 6),
            default => trim((string) $phone),
        };
    }
}

if (! function_exists('getYears')) {
    /** @return array<int, int> */
    function getYears(int $range = 10): array
    {
        $currentYear = (int) now()->format('Y');

        return range($currentYear, $currentYear + max(0, $range));
    }
}

if (! function_exists('generateRandomPassword')) {
    function generateRandomPassword(int $length = 12): string
    {
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*';
        $password = '';
        $max = strlen($alphabet) - 1;

        for ($i = 0; $i < max(8, $length); $i++) {
            $password .= $alphabet[random_int(0, $max)];
        }

        return $password;
    }
}

if (! function_exists('getToken')) {
    function getToken(string $type): string|int
    {
        return match ($type) {
            'email' => hash_hmac('sha256', Str::random(40), (string) config('app.key')),
            default => random_int(123456, 987654),
        };
    }
}

if (! function_exists('siteSettings')) {
    function siteSettings(): SiteSettingRepositoryInterface
    {
        return app(SiteSettingRepositoryInterface::class);
    }
}

if (! function_exists('getSiteSetting')) {
    function getSiteSetting(): ?SiteSetting
    {
        try {
            return once(static fn () => Cache::remember(
                'site-setting:model',
                now()->addHour(),
                static fn (): ?SiteSetting => SiteSetting::query()->first()
            ));
        } catch (Throwable $exception) {
            Log::warning('Unable to load site setting.', [
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }
}

if (! function_exists('siteAssetFromSetting')) {
    function siteAssetFromSetting(string $field, string $pathAttribute, string $fallback): string
    {
        $setting = getSiteSetting();
        $paths = $setting?->{$pathAttribute};

        return filled($setting?->{$field}) && is_array($paths) && filled($paths['original'] ?? null)
            ? $paths['original']
            : asset($fallback);
    }
}

if (! function_exists('getFavIcon')) {
    function getFavIcon(): string
    {
        return siteAssetFromSetting('fav_icon', 'fav_icon_path', 'front/img/fav.png');
    }
}

if (! function_exists('getLogo')) {
    function getLogo(): string
    {
        return siteAssetFromSetting('logo', 'logo_path', 'front/img/logo.png');
    }
}

if (! function_exists('getFooterLogo')) {
    function getFooterLogo(): string
    {
        return siteAssetFromSetting('footer_logo', 'footer_logo_path', 'front/img/logo_footer.png');
    }
}

if (! function_exists('getAppLogo')) {
    function getAppLogo(): string
    {
        return siteAssetFromSetting('app_logo', 'app_logo_path', 'front/img/logo.png');
    }
}

if (! function_exists('getLoginBackground')) {
    function getLoginBackground(): ?string
    {
        try {
            if (app()->bound(SiteSettingRepositoryInterface::class)) {
                $url = siteSettings()->imageUrl('login_bg_path');

                if (filled($url)) {
                    return (string) $url;
                }
            }
        } catch (Throwable) {
            // Fall back to the model-backed path below.
        }

        $setting = getSiteSetting();

        foreach (['login_bg_image_path', 'login_bg_path'] as $attribute) {
            $paths = $setting?->{$attribute};

            if (is_array($paths) && filled($paths['original'] ?? null)) {
                return (string) $paths['original'];
            }
        }

        return null;
    }
}

if (! function_exists('getSiteSettingLogos')) {
    /**
     * @return array{
     *     logo:string,
     *     favicon:string,
     *     app_logo:string,
     *     footer_logo:string,
     *     login_bg_image:string|null,
     *     company_name:string|null,
     *     slogan:string|null
     * }
     */
    function getSiteSettingLogos(): array
    {
        $setting = getSiteSetting();

        return [
            'logo' => $setting?->logo_path['original'] ?? asset('front/img/logo.png'),
            'favicon' => $setting?->fav_icon_path['original'] ?? asset('front/img/fav.png'),
            'app_logo' => $setting?->app_logo_path['original'] ?? asset('front/img/logo.png'),
            'footer_logo' => $setting?->footer_logo_path['original'] ?? asset('front/img/logo_footer.png'),
            'login_bg_image' => filled($setting?->login_bg_image)
                ? siteAssetFromSetting('login_bg_image', 'login_bg_image_path', '')
                : null,
            'company_name' => $setting?->company_name,
            'slogan' => $setting?->slogan,
        ];
    }
}

if (! function_exists('getEmailTemplate')) {
    function getEmailTemplate(string $role, string $type): ?EmailTemplate
    {
        return EmailTemplate::query()
            ->whereRole($role)
            ->whereType($type)
            ->whereIsActive(true)
            ->first();
    }
}

if (! function_exists('normalizeEmailTemplateInputs')) {
    /** @return array<int, string> */
    function normalizeEmailTemplateInputs(mixed $acceptedInputs): array
    {
        if (is_string($acceptedInputs)) {
            $acceptedInputs = explode(',', $acceptedInputs);
        }

        if (! is_iterable($acceptedInputs)) {
            return [];
        }

        $normalized = [];
        foreach ($acceptedInputs as $input) {
            $input = trim((string) $input);

            if ($input !== '') {
                $normalized[] = $input;
            }
        }

        return array_values(array_unique($normalized));
    }
}

if (! function_exists('getEmailRawTag')) {
    function getEmailRawTag(string $tag): mixed
    {
        return config('email-template.tags.'.$tag);
    }
}

if (! function_exists('renderEmailHTML')) {
    /** @param iterable<int, string> $acceptedTags */
    function renderEmailHTML(string $description, iterable $acceptedTags): string
    {
        foreach ($acceptedTags as $tag) {
            $description = preg_replace(
                '/\{!-'.preg_quote(trim($tag), '/').'-!\}/i',
                (string) getEmailRawTag(trim($tag)),
                $description
            ) ?? $description;
        }

        return $description;
    }
}

if (! function_exists('renderEmailPlainText')) {
    function renderEmailPlainText(mixed $value): string
    {
        $text = (string) $value;

        $text = preg_replace(
            '/<(br|\/p|\/div|\/li|\/h[1-6])\b[^>]*>/i',
            "\n",
            $text
        ) ?? $text;

        return trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}

if (! function_exists('renderEmailData')) {
    /** @param iterable<int, string> $acceptedInputs @param array<string, mixed> $acceptedData */
    function renderEmailData(string $content, iterable $acceptedInputs, array $acceptedData): string
    {
        foreach ($acceptedInputs as $input) {
            $key = trim($input);

            if (! array_key_exists($key, $acceptedData)) {
                continue;
            }

            $value = $acceptedData[$key];

            if ($key === 'note') {
                $value = renderEmailPlainText($value);
            }

            $content = preg_replace(
                '/\{\{\s*\$?'.preg_quote($key, '/').'\s*\}\}/',
                e((string) $value),
                $content
            ) ?? $content;
        }

        return $content;
    }
}

if (! function_exists('renderLoopData')) {
    /** @param array<int, iterable<int, array<string, mixed>>> $loopData */
    function renderLoopData(string $description, array $loopData): string
    {
        foreach ($loopData as $key => $loop) {
            $pattern = '{--loop--'.($key + 1).'}';

            if (! Str::contains($description, $pattern)) {
                continue;
            }

            $html = '';
            foreach ($loop as $data) {
                $html .= '<ul>';
                foreach ($data as $index => $info) {
                    $label = e(Str::headline((string) $index));
                    $html .= '<li><b>'.$label.': </b>'.e((string) $info).'</li>';
                }
                $html .= '</ul><hr>';
            }

            $description = str_replace($pattern, $html, $description);
        }

        return $description;
    }
}

if (! function_exists('buildTableNameToLogInfoTitle')) {
    function buildTableNameToLogInfoTitle(string $tableName): string
    {
        return Str::headline(
            Str::of($tableName)
                ->explode('_')
                ->map(static fn (string $part): string => Str::singular($part))
                ->implode(' ')
        );
    }
}

if (! function_exists('setSMTP')) {
    function setSMTP(): void
    {
        if (app()->environment('local')) {
            return;
        }

        $setting = getSiteSetting();
        if (! $setting) {
            return;
        }

        // ℹ️ Never bail out quietly: without site-setting credentials the mailer
        // silently falls back to the .env values, and a stale token there surfaces
        // as "535 Authentication Failed" — which looks like wrong credentials
        // rather than missing configuration. Say so in the log.
        if (blank($setting->mail_host) || blank($setting->mail_user_name) || blank($setting->mail_password)) {
            Log::warning('SMTP is not fully configured in site settings; falling back to the .env mail config.', [
                'has_host' => filled($setting->mail_host),
                'has_username' => filled($setting->mail_user_name),
                'has_password' => filled($setting->mail_password),
            ]);

            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.scheme', $setting->mail_port == 465 ? 'smtps' : 'smtp');
        Config::set('mail.mailers.smtp.encryption', $setting->mail_encryption ?: 'tls');
        Config::set('mail.mailers.smtp.host', $setting->mail_host);
        if (filled($setting->mail_port ?? null)) {
            Config::set('mail.mailers.smtp.port', $setting->mail_port);
        }
        if (filled($setting->mail_user_name ?? null)) {
            Config::set('mail.mailers.smtp.username', $setting->mail_user_name);
        }
        if (filled($setting->mail_password ?? null)) {
            Config::set('mail.mailers.smtp.password', $setting->mail_password);
        }
        if (filled($setting->mail_sender_address ?? null)) {
            Config::set('mail.from.address', $setting->mail_sender_address);
        }
        if (filled($setting->mail_sender_name ?? null)) {
            Config::set('mail.from.name', $setting->mail_sender_name);
        }

        Mail::forgetMailers();
    }
}

if (! function_exists('getImagePath')) {
    /** @return array{original:string, thumb?:string}|null */
    function getImagePath(string $uploadPath, ?string $imageName, bool $signed = false): ?array
    {
        if (blank($imageName)) {
            return null;
        }

        $fileType = checkFileType($imageName);
        if ($fileType === 'other') {
            return null;
        }

        $basePath = trim($uploadPath, '/').'/'.ltrim($imageName, '/');
        $thumbPath = trim($uploadPath, '/').'/thumb/'.ltrim($imageName, '/');

        if (getStorageType() !== 'local' && app()->environment('production')) {
            $originalUrl = s3_image_url(buildUploadPathUrl($basePath), $signed);
            $thumbUrl = $fileType === 'image' ? s3_image_url(buildUploadPathUrl($thumbPath), $signed) : null;
        } else {
            $originalUrl = asset($basePath);
            $thumbUrl = $fileType === 'image' ? asset($thumbPath) : null;
        }

        return array_filter([
            'original' => $originalUrl,
            'thumb' => $thumbUrl,
        ], static fn (?string $value): bool => filled($value));
    }
}

if (! function_exists('checkFileType')) {
    function checkFileType(?string $imageName): string
    {
        $extension = Str::lower(pathinfo((string) $imageName, PATHINFO_EXTENSION));

        return match (true) {
            in_array($extension, [
                'jpeg',
                'jpg',
                'png',
                'ico',
                'webp',
                'svg',
                'gif',
                'bmp',
                'tiff',
                'tif',
                'heic',
                'heif',
                'avif',
                'jfif',
                'pjpeg',
                'pjp',
            ], true) => 'image',
            in_array($extension, ['docx', 'doc'], true) => 'doc',
            in_array($extension, ['csv', 'txt'], true) => 'csv',
            in_array($extension, ['xls', 'xlsx'], true) => 'xls',
            $extension === 'pdf' => 'pdf',
            $extension !== '' => 'file',
            default => 'other',
        };
    }
}

if (! function_exists('s3_image_url')) {
    function s3_image_url(string $path, bool $signed = false): ?string
    {
        setStorageConfig();
        $diskName = getStorageType();

        if ($diskName === 'local') {
            return null;
        }

        $disk = Storage::disk($diskName);
        $normalizedPath = normalizeStorageObjectPath($path);

        return $signed
            ? $disk->temporaryUrl($normalizedPath, now()->addMinutes(10))
            : $disk->url($normalizedPath);
    }
}

if (! function_exists('setStorageConfig')) {
    function setStorageConfig(): void
    {
        $setting = getSiteSetting();
        $storageType = $setting?->storage_type;

        if (! in_array($storageType, ['aws', 'wasabi'], true)) {
            return;
        }

        Config::set("filesystems.disks.{$storageType}.driver", 's3');
        Config::set("filesystems.disks.{$storageType}.key", $setting->storage_access_key);
        Config::set("filesystems.disks.{$storageType}.secret", $setting->storage_secret_key);
        Config::set("filesystems.disks.{$storageType}.region", $setting->storage_region);
        Config::set("filesystems.disks.{$storageType}.bucket", $setting->storage_bucket_name);
        Config::set("filesystems.disks.{$storageType}.endpoint", $setting->storage_endpoint);

        if ($storageType === 'aws') {
            Config::set('filesystems.disks.aws.url', storagePublicUrl($setting));
            Config::set('filesystems.disks.aws.use_path_style_endpoint', true);
        }

        if ($storageType === 'wasabi') {
            Config::set('filesystems.disks.wasabi.use_path_style_endpoint', true);
            Config::set('filesystems.disks.wasabi.bucket_endpoint', true);
        }
    }
}

if (! function_exists('getStorageType')) {
    function getStorageType(): string
    {
        return getSiteSetting()?->storage_type ?: 'local';
    }
}

if (! function_exists('buildUploadPathUrl')) {
    function buildUploadPathUrl(string $path): string
    {
        // Stored paths are not always clean object keys — legacy rows carry a
        // full CDN URL. Strip the host first, otherwise the key we build is
        // 'local/cdn.example.com/uploads/…' and matches no object. No-op for
        // paths that are already keys.
        $path = normalizeStorageObjectPath($path);

        return app()->environment('production') ? $path : 'local/'.$path;
    }
}

if (! function_exists('storagePublicUrl')) {
    function storagePublicUrl(?object $setting = null): ?string
    {
        $setting ??= getSiteSetting();
        $url = $setting?->storage_url;

        if (is_string($url) && filled($url)) {
            return normalizeStorageBaseUrl($url);
        }

        $endpoint = $setting?->storage_endpoint;

        return is_string($endpoint) && filled($endpoint) ? normalizeStorageBaseUrl($endpoint) : null;
    }
}

if (! function_exists('normalizeStorageBaseUrl')) {
    function normalizeStorageBaseUrl(string $url): string
    {
        $url = rtrim(trim($url), '/');
        if ($url === '') {
            return '';
        }

        $url = preg_match('/^https?:\/\//i', $url) === 1 ? $url : 'https://'.$url;
        $parts = parse_url($url);
        $path = isset($parts['path']) ? trim((string) $parts['path'], '/') : '';

        if ($path !== 'uploads') {
            return $url;
        }

        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? null;

        return is_string($host) && $host !== '' ? $scheme.'://'.$host : $url;
    }
}

if (! function_exists('normalizeStorageObjectPath')) {
    function normalizeStorageObjectPath(mixed $path): string
    {
        $path = str_replace('\\', '/', trim((string) $path));
        if ($path === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $path) === 1) {
            $path = (string) parse_url($path, PHP_URL_PATH);
        }

        $path = ltrim($path, '/');

        foreach (storageUrlHostPrefixes() as $prefix) {
            if (str_starts_with($path, $prefix.'/')) {
                return ltrim(substr($path, strlen($prefix) + 1), '/');
            }
        }

        return $path;
    }
}

if (! function_exists('storageUrlHostPrefixes')) {
    /** @return array<int, string> */
    function storageUrlHostPrefixes(): array
    {
        $prefixes = [];

        foreach (['aws', 'wasabi', 's3'] as $disk) {
            foreach (['url', 'endpoint'] as $key) {
                $value = Config::get("filesystems.disks.{$disk}.{$key}");
                if (! is_string($value) || blank($value)) {
                    continue;
                }

                $url = normalizeStorageBaseUrl($value);
                $host = parse_url($url, PHP_URL_HOST);
                $basePath = parse_url($url, PHP_URL_PATH);

                if (! is_string($host) || $host === '') {
                    continue;
                }

                $prefixes[] = trim($host, '/');

                if (is_string($basePath) && filled(trim($basePath, '/'))) {
                    $prefixes[] = trim($host, '/').'/'.trim($basePath, '/');
                }
            }
        }

        return array_values(array_unique($prefixes));
    }
}

if (! function_exists('getHashedPassword')) {
    function getHashedPassword(string $value): string
    {
        return Hash::make($value);
    }
}

if (! function_exists('getAdminEmails')) {
    /** @return array<int, string> */
    function getAdminEmails(): array
    {
        return AdminUser::query()
            ->where('is_active', true)
            ->where('has_email_access', true)
            ->pluck('email')
            ->unique()
            ->values()
            ->all();
    }
}

if (! function_exists('convertDate')) {
    function convertDate(?string $date, string $option = 'ad'): ?string
    {
        if (blank($date)) {
            return null;
        }

        $parts = array_map('intval', explode('-', $date));
        if (count($parts) !== 3) {
            return null;
        }

        [$year, $month, $day] = $parts;
        $converter = new NepaliDate;
        $converted = $option === 'ad'
            ? $converter->convertBsToAd($year, $month, $day)
            : $converter->convertAdToBs($year, $month, $day);

        return sprintf('%04d-%02d-%02d', $converted['year'], $converted['month'], $converted['day']);
    }
}

if (! function_exists('siteSettingCache')) {
    function siteSettingCache(): mixed
    {
        return Cache::remember('site_setting_data', now()->addHour(), static function (): mixed {
            return app('App\Services\Cms\SiteSetting\SiteSettingService')->page();
        });
    }
}

if (! function_exists('encryptId')) {
    function encryptId(int|string $id): string
    {
        return rtrim(strtr(Crypt::encryptString((string) $id), '+/', '-_'), '=');
    }
}

if (! function_exists('decryptId')) {
    function decryptId(string $value): string
    {
        $base64 = strtr($value, '-_', '+/').str_repeat('=', (4 - strlen($value) % 4) % 4);

        return Crypt::decryptString($base64);
    }
}

if (! function_exists('getFooterMenu')) {
    function getFooterMenu(): ?Menu
    {
        return once(static fn () => Cache::remember('menu:footer', now()->addHour(), static fn () => Menu::query()
            ->where('header', false)
            ->where('is_active', true)
            ->with('items')
            ->first()));
    }
}

if (! function_exists('getHeaderMenu')) {
    function getHeaderMenu(): ?Menu
    {
        return once(static fn () => Cache::remember('menu:header', now()->addHour(), static fn () => Menu::query()
            ->where('header', true)
            ->where('is_active', true)
            ->with('items')
            ->first()));
    }
}
