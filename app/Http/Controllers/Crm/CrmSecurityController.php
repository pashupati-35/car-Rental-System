<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Admin\Auth\MFAController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\Security\UpdateCrmPasswordRequest;
use App\Http\Resources\AdminResource;
use App\Services\Crm\CrmSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CrmSecurityController extends Controller
{
    public function __construct(
        protected MFAController $mfaController,
        protected CrmSecurityService $crmSecurityService
    ) {}

    /**
     * Display the CRM Account Security & MFA configuration view.
     */
    public function index(Request $request): Response
    {
        $admin = Auth::guard('admin')->user();
        $adminResource = $admin ? (new AdminResource($admin))->resolve() : null;

        return Inertia::render('crm/Security', [
            'user' => $adminResource,
        ]);
    }

    /**
     * Update password from the CRM portal.
     */
    public function updatePassword(UpdateCrmPasswordRequest $request)
    {
        $admin = Auth::guard('admin')->user();
        $this->crmSecurityService->updatePassword($admin, $request->validated('password'));

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'CRM Account password updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'CRM Account password updated successfully.');
    }

    /**
     * Generate TOTP secret and QR code for CRM staff.
     */
    public function generateMfa(Request $request)
    {
        return $this->mfaController->generate($request);
    }

    /**
     * Activate TOTP authenticator with verification code.
     */
    public function activateMfa(Request $request)
    {
        return $this->mfaController->activate($request);
    }

    /**
     * Deactivate TOTP authenticator.
     */
    public function deactivateMfa(Request $request)
    {
        return $this->mfaController->deactivate($request);
    }

    /**
     * Toggle Email 2-Factor Authentication (activate).
     */
    public function activateEmailAuth(Request $request)
    {
        return $this->mfaController->activateEmailAuthenticator($request);
    }

    /**
     * Toggle Email 2-Factor Authentication (deactivate).
     */
    public function deactivateEmailAuth(Request $request)
    {
        return $this->mfaController->deactivateEmailAuthenticator($request);
    }
}
