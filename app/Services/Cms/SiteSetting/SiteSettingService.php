<?php

namespace App\Services\Cms\SiteSetting;

use App\Http\Resources\Cms\SiteSetting\ColorSettingResource;
use App\Http\Resources\Cms\SiteSetting\SiteSettingResource;
use App\Mail\SiteSetting\SMTPTestEmail;
use App\Repositories\Cms\SiteSettingRepositoryInterface;
use App\Services\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class SiteSettingService extends Service
{
    public $uploadPath = 'setting';

    private const IMAGE_COLUMNS = [
        'logo',
        'app_logo',
        'footer_logo',
        'fav_icon',
        'login_bg_image',
        'email_logo_image',
    ];

    private const BOOLEAN_COLUMNS = [
        'enable_cookies',
        'is_admission_form_active',
        'display_storage',
        'display_smtp',
    ];

    private const SECRET_COLUMNS = [
        'mail_password',
        'storage_access_key',
        'storage_secret_key',
        'recaptcha_secret_key',
    ];

    private const CREDENTIAL_COLUMNS = [
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_user_name',
        'mail_encryption',
        'storage_type',
        'storage_endpoint',
        'storage_region',
        'storage_bucket_name',
        'storage_url',
        'recaptcha_site_key',
    ];

    public function __construct(protected SiteSettingRepositoryInterface $settingRepo) {}

    public function store(array $data)
    {
        return $this->save($data);
    }

    public function update($id, array $data)
    {
        return $this->save($data, $id);
    }

    private function save(array $data, ?int $id = null)
    {
        $setting = $id ? $this->settingRepo->find($id) : $this->settingRepo->getSettings();
        if (!$setting) {
            $setting = $this->settingRepo->getSettings();
        }

        foreach (self::BOOLEAN_COLUMNS as $column) {
            if (array_key_exists($column, $data)) {
                $data[$column] = filter_var($data[$column], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }

        $data = $this->handleImageUploads($setting, $data);
        $data = $this->keepStoredCredentials($setting, $data);

        return $this->settingRepo->update($setting->id, $data);
    }

    private function keepStoredCredentials($setting, array $data): array
    {
        foreach ([...self::SECRET_COLUMNS, ...self::CREDENTIAL_COLUMNS] as $column) {
            if (array_key_exists($column, $data) && blank($data[$column]) && filled($setting->{$column})) {
                unset($data[$column]);
            }
        }

        return $data;
    }

    private function handleImageUploads($setting, array $data): array
    {
        foreach (self::IMAGE_COLUMNS as $column) {
            $isRemoval = filter_var($data["remove_$column"] ?? false, FILTER_VALIDATE_BOOLEAN);
            $file = $data[$column] ?? null;

            unset($data["remove_$column"]);

            if (!$isRemoval && !$file instanceof UploadedFile) {
                unset($data[$column]);
                continue;
            }

            if (!empty($setting->{$column})) {
                $this->deleteFile($this->uploadPath, $setting->{$column});
            }

            $data[$column] = $isRemoval ? null : $this->uploadFile($file, $this->uploadPath);
        }

        return $data;
    }

    public function all()
    {
        $setting = $this->settingRepo->all();
        return SiteSettingResource::collection($setting);
    }

    public function getSiteSetting()
    {
        $setting = $this->settingRepo->getSettings();
        return $setting ? new SiteSettingResource($setting) : null;
    }

    public function getSettingColors()
    {
        $setting = $this->settingRepo->getSettings();
        return $setting ? new ColorSettingResource($setting) : null;
    }

    public function sendTestEmail(string $email): bool
    {
        try {
            Mail::to($email)->send(new SMTPTestEmail);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
