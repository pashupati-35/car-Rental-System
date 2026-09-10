<?php

namespace App\Services\Cms\SiteSetting;

use App\Http\Resources\Cms\SiteSetting\ColorSettingResource;
use App\Http\Resources\Cms\SiteSetting\SiteSettingResource;
use App\Mail\SiteSetting\SMTPTestEmail;
use App\Models\Cms\SiteSetting\SiteSetting;
use App\Repositories\Interfaces\Cms\SiteSetting\SiteSettingRepositoryInterface;
use App\Services\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class SiteSettingService extends Service
{
    protected $setting;

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

    public function __construct(SiteSetting $setting, protected SiteSettingRepositoryInterface $settings)
    {
        $this->setting = $setting;
    }

    public function refresh()
    {
        return $this->settings->refresh();
    }

    public function store($data)
    {
        return $this->save($data);
    }

    public function update($id, $data)
    {
        return $this->save($data, $id);
    }

    private function save(array $data, ?int $id = null): bool
    {
        $setting = $id
            ? $this->setting->newQuery()->find($id)
            : $this->settings->current();

        $setting ??= $this->setting->newInstance();

        foreach (self::BOOLEAN_COLUMNS as $column) {
            if (array_key_exists($column, $data)) {
                $data[$column] = filter_var($data[$column], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }

        $data = $this->handleImageUploads($setting, $data);
        $data = $this->keepStoredCredentials($setting, $data);

        return (bool) $setting->fill($data)->save();
    }

    private function keepStoredCredentials(SiteSetting $setting, array $data): array
    {
        foreach ([...self::SECRET_COLUMNS, ...self::CREDENTIAL_COLUMNS] as $column) {
            if (array_key_exists($column, $data) && blank($data[$column]) && filled($setting->{$column})) {
                unset($data[$column]);
            }
        }

        return $data;
    }

    private function handleImageUploads(SiteSetting $setting, array $data): array
    {
        foreach (self::IMAGE_COLUMNS as $column) {
            $isRemoval = filter_var($data["remove_$column"] ?? false, FILTER_VALIDATE_BOOLEAN);
            $file = $data[$column] ?? null;

            unset($data["remove_$column"]);

            if (! $isRemoval && ! $file instanceof UploadedFile) {
                unset($data[$column]);

                continue;
            }

            if (! empty($setting->{$column})) {
                $this->deleteFile($this->uploadPath, $setting->{$column});
            }

            $data[$column] = $isRemoval ? null : $this->uploadFile($file, $this->uploadPath);
        }

        return $data;
    }

    public function all()
    {
        $setting = $this->setting->get();

        return SiteSettingResource::collection($setting);
    }

    public function getSiteSetting()
    {
        $setting = $this->settings->current();

        if (! empty($setting)) {
            return new SiteSettingResource($setting);
        }

        return null;
    }

    public function getSettingColors()
    {
        $setting = $this->settings->current();

        if (! empty($setting)) {
            return new ColorSettingResource($setting);
        }

        return null;
    }

    public function delete($id)
    {
        try {
            $setting = $this->settings->current();

            return $setting ? $setting->delete() : false;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->setting->where($column, $value)->first();
    }

    public function testAwsUpload($file)
    {
        $uploadPath = 'test';
        $data = $this->uploadFile($file, $uploadPath);
        $path = $uploadPath.'/'.$data;
        if (env('APP_ENV') != 'production') {
            $path = 'local/'.$path;
        }
        $url = s3_image_url($path);

        return $url;
    }

    public function testS3($type = 'files')
    {
        return $this->listFilesAndFolder($type);
    }

    public function sendTestEmail($email)
    {
        setSMTP();
        Mail::to($email)->send(new SMTPTestEmail);

        return true;
    }
}
