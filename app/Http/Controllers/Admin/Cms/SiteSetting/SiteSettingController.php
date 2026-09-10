<?php

namespace App\Http\Controllers\Admin\Cms\SiteSetting;

use App\Http\Controllers\Controller;
use App\Services\Cms\SiteSetting\SiteSettingService;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public $setting;

    public function __construct(SiteSettingService $setting)
    {
        $this->setting = $setting;
    }

    public function index()
    {
        $siteSetting = $this->setting->getSiteSetting();

        return response(['siteSetting' => $siteSetting], 200);
    }

    public function all()
    {
        $siteSetting = $this->setting->all();

        return response(['sitesetting' => $siteSetting]);
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
            $this->setting->refresh();

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

    public function show()
    {
        if ($setting = $this->setting->getSiteSetting()) {
            return response(['status' => 'OK', 'setting' => $setting], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function getSettingColors()
    {
        $response = $this->setting->getSettingColors();

        return response()->json($response);
    }

    public function testAwsUpload(Request $request)
    {
        $file = $request->file('file');
        $response = $this->setting->testAwsUpload($file);
        if ($response) {
            return response(['status' => 'OK', 'path' => $response], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function testS3()
    {
        $response = $this->setting->testS3();

        return response()->json($response);
    }

    public function sendTestEmail(Request $request)
    {
        $email = $request->get('email');
        $response = $this->setting->sendTestEmail($email);
        if ($response) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}
