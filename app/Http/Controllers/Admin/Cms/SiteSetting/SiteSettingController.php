<?php

namespace App\Http\Controllers\Admin\Cms\SiteSetting;

use App\Http\Controllers\Controller;
use App\Services\Cms\SiteSetting\SiteSettingService;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function __construct(protected SiteSettingService $settingService) {}

    public function index()
    {
        $siteSetting = $this->settingService->getSiteSetting();
        return response()->json(['data' => $siteSetting], 200);
    }

    public function all()
    {
        $siteSetting = $this->settingService->getSiteSetting();
        return response()->json(['data' => $siteSetting], 200);
    }

    public function store(Request $request)
    {
        $setting = $this->settingService->store($request->all());
        if ($setting) {
            return response()->json(['status' => 'OK', 'message' => 'Settings saved successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to save settings.'], 500);
    }

    public function update(Request $request, $id)
    {
        $setting = $this->settingService->update($id, $request->all());
        if ($setting) {
            return response()->json(['status' => 'OK', 'message' => 'Settings updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update settings.'], 500);
    }

    public function getSettingColors()
    {
        $colors = $this->settingService->getSettingColors();
        return response()->json(['data' => $colors], 200);
    }

    public function sendTestEmail(Request $request)
    {
        $email = $request->input('email');
        if ($this->settingService->sendTestEmail($email)) {
            return response()->json(['status' => 'OK', 'message' => 'Test email sent successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to send test email.'], 500);
    }
}
