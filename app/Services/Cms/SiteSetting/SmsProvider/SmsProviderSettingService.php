<?php

namespace App\Services\Cms\SiteSetting\SmsProvider;

use App\Http\Resources\Cms\SiteSetting\SmsProvider\SmsProviderSettingResource;
use App\Models\Cms\SiteSetting\SmsProvider\SmsProviderSetting;
use App\Services\Service;

class SmsProviderSettingService extends Service
{
    protected $sms;

    public $uploadPath = 'sms';

    public function __construct(SmsProviderSetting $sms)
    {
        $this->sms = $sms;
    }

    public function paginate($limit = 25)
    {
        $sms = $this->sms->orderBy('id', 'DESC')->paginate($limit);

        return SmsProviderSettingResource::collection($sms);
    }

    public function all()
    {
        $sms = $this->sms->orderBy('id', 'DESC')->get();

        return SmsProviderSettingResource::collection($sms);
    }

    public function store($data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;
            $provider = $this->sms->create($data);
            if ($data['is_active']) {
                return $providers = $this->sms->where('id', '!=', $provider->id)->update(['is_active' => 0]);
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;
            $sms = $this->findByColumn('id', $id);
            $sms->update($data);
            if ($data['is_active']) {
                return $providers = $this->sms->where('id', '!=', $id)->update(['is_active' => 0]);
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $sms = $this->findByColumn('id', $id);

            return $sms->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        $setting = $this->sms->where($column, $value)->first();
        if (! empty($setting)) {
            return new SmsProviderSettingResource($setting);
        }

        return null;
    }

    public function sendTestSms() {}
}
