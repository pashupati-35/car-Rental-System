<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\Authenticator\Authenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MFAController extends Controller
{
    public function __construct(protected Authenticator $authenticator) {}

    /**
     * Check if Admin has MFA enabled before logging in.
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->input('email'))->first();

        if ($admin && Hash::check($request->input('password'), $admin->password)) {
            return response()->json([
                'status' => 'OK',
                'data' => [
                    'id' => $admin->id,
                    'email' => $admin->email,
                    'name' => $admin->name,
                    'is_mfa_enabled' => (bool)$admin->is_mfa_enabled,
                ],
            ]);
        }

        return response()->json([
            'status' => 'ERROR',
            'errors' => 'Invalid email or password.',
        ], 401);
    }

    /**
     * Verify Admin MFA code and login.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'verification_code' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->input('email'))->first();

        if ($admin && Hash::check($request->input('password'), $admin->password)) {
            if ($this->authenticator->verifyCode($admin->mfa_secret_code, $request->input('verification_code'), 2)) {
                Auth::guard('admin')->login($admin, $request->boolean('remember'));
                $request->session()->regenerate();

                return response()->json([
                    'status' => 'OK',
                    'message' => 'Authentication successful.',
                    'redirect' => route('admin.dashboard'),
                ]);
            }

            return response()->json([
                'status' => 'ERROR',
                'errors' => 'The MFA verification code is invalid.',
            ], 422);
        }

        return response()->json([
            'status' => 'ERROR',
            'errors' => 'Invalid credentials.',
        ], 401);
    }

    /**
     * Generate Admin MFA Setup QR code.
     */
    public function generate(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $secret = $admin->mfa_secret_code ?: $this->authenticator->createSecret();
        $appName = config('app.name', 'CarRentalSystem') . ' Admin';
        $qrCodeUrl = $this->authenticator->getQRCodeGoogleUrl($admin->email, $secret, $appName);

        return response()->json([
            'status' => 'OK',
            'secret_key' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    /**
     * Activate MFA for Admin.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'secret_key' => 'required|string',
            'verification_code' => 'required|string',
        ]);

        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $secret = $request->input('secret_key');
        $code = $request->input('verification_code');

        if ($this->authenticator->verifyCode($secret, $code, 2)) {
            $admin->is_mfa_enabled = true;
            $admin->mfa_secret_code = $secret;
            $admin->save();

            return response()->json([
                'status' => 'OK',
                'message' => 'Admin MFA successfully enabled.',
            ]);
        }

        return response()->json([
            'status' => 'ERROR',
            'message' => 'Invalid verification code.',
        ], 422);
    }

    /**
     * Deactivate MFA for Admin.
     */
    public function deactivate(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $admin->is_mfa_enabled = false;
        $admin->mfa_secret_code = null;
        $admin->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Admin MFA successfully disabled.',
        ]);
    }
}
