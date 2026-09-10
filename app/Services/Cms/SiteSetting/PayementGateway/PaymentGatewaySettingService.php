<?php

namespace App\Services\Cms\SiteSetting\PayementGateway;

use App\Http\Resources\Cms\SiteSetting\PaymentGateway\PaymentGatewaySettingResource;
use App\Models\Cms\SiteSetting\PaymentGateway\PaymentGatewaySetting;
use App\Services\Service;

class PaymentGatewaySettingService extends Service
{
    protected $setting;

    public $uploadPath = 'setting';

    public function __construct(PaymentGatewaySetting $setting)
    {
        $this->setting = $setting;
    }

    public function paginate($limit = 25)
    {
        $setting = $this->setting->orderBy('id', 'DESC')->paginate($limit);

        return PaymentGatewaySettingResource::collection($setting);
    }

    public function all()
    {
        $setting = $this->setting->orderBy('id', 'DESC')->get();

        return PaymentGatewaySettingResource::collection($setting);
    }

    public function store($data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;

            return $this->setting->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;
            $setting = $this->findByColumn('id', $id);

            return $setting->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $setting = $this->findByColumn('id', $id);

            return $setting->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        $setting = $this->setting->where($column, $value)->first();
        if (! empty($setting)) {
            return new PaymentGatewaySettingResource($setting);
        }

        return null;
    }

    public function getAvailablePaymentGateways()
    {
        $paymentGateways = [
            'is_esewa_enabled' => 0,
            'is_khalti_enabled' => 0,
            'is_connect_ips_enabled' => 0,
            'khalti_pk' => null,
            'esewa_mid' => null,
        ];
        $gateways = $this->setting->whereIsActive(1)->get();
        if ($gateways->count() > 0) {
            foreach ($gateways as $gateway) {
                if ($gateway->type == 'connect_ips' && ! empty($gateway->app_id) && ! empty($gateway->app_name) && ! empty($gateway->user_name) && ! empty($gateway->password) && ! empty($gateway->pfx_password)) {
                    $paymentGateways['is_connect_ips_enabled'] = 1;
                }
                if ($gateway->type == 'esewa' && ! empty($gateway->merchant_id)) {
                    $paymentGateways['is_esewa_enabled'] = 1;
                    $paymentGateways['esewa_mid'] = $gateway->merchant_id;
                }
                if ($gateway->type == 'khalti' && ! empty($gateway->public_key) && ! empty($gateway->private_key)) {
                    $paymentGateways['is_khalti_enabled'] = 1;
                    $paymentGateways['khalti_pk'] = $gateway->public_key;
                }
            }
        }

        return $paymentGateways;
    }
}
