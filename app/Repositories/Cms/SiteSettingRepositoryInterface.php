<?php

namespace App\Repositories\Cms;

use App\Models\Cms\SiteSetting\SiteSetting;
use App\Repositories\BaseRepositoryInterface;

interface SiteSettingRepositoryInterface extends BaseRepositoryInterface
{
    public function getSettings(): ?SiteSetting;

    public function current(): ?SiteSetting;

    public function updateSettings(array $data): SiteSetting;
}
