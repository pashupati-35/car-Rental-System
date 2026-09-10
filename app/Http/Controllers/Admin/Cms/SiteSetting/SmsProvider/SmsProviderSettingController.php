<?php

namespace App\Http\Controllers\Admin\Cms\SiteSetting\SmsProvider;

use App\Http\Controllers\Controller;
use App\Services\Cms\SiteSetting\SmsProvider\SmsProviderSettingService;
use Illuminate\Http\Request;

class SmsProviderSettingController extends Controller
{
    public $setting;

    public function __construct(SmsProviderSettingService $setting)
    {
        $this->setting = $setting;
    }

    public function index()
    {
        return response($this->setting->all());
    }

    public function store(Request $request)
    {
        $setting = $this->setting->store($request->all());
        if ($setting) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(Request $request, $id)
    {
        $setting = $this->setting->update($id, $request->all());
        if ($setting) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->setting->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($setting = $this->setting->findByColumn('id', $id)) {
            return response(['status' => 'OK', 'setting' => $setting], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}
