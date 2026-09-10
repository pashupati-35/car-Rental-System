<?php

namespace App\Repositories\Cms;

use App\Models\Cms\SiteSetting\SiteSetting;
use App\Repositories\BaseRepository;

class SiteSettingRepository extends BaseRepository implements SiteSettingRepositoryInterface
{
    public function __construct(SiteSetting $model)
    {
        parent::__construct($model);
    }

    public function getSettings(): ?SiteSetting
    {
        return $this->model->first() ?? $this->model->create([]);
    }

    public function current(): ?SiteSetting
    {
        return $this->getSettings();
    }

    public function updateSettings(array $data): SiteSetting
    {
        $setting = $this->getSettings();
        $setting->update($data);
        return $setting->fresh();
    }
}
