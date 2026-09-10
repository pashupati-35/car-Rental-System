<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Services\Authenticator\Authenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MFAController extends Controller
{
    public function __construct(protected Authenticator $authenticator) {}

    /**
     * Check if Owner has MFA enabled before logging in.
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $owner = Owner::where('email', $request->input('email'))->first();

        if ($owner && Hash::check($request->input('password'), $owner->password)) {
            return response()->json([
                'status' => 'OK',
                'data' => [
                    'id' => $owner->id,
                    'email' => $owner->email,
                    'name' => $owner->full_name,
                    'is_mfa_enabled' => (bool)$owner->is_mfa_enabled,
                ],
            ]);
        }

        return response()->json([
            'status' => 'ERROR',
            'errors' => 'Invalid email or password.',
        ], 401);
    }

    /**
     * Verify Owner MFA code and login.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'verification_code' => 'required|string',
        ]);

        $owner = Owner::where('email', $request->input('email'))->first();

        if ($owner && Hash::check($request->input('password'), $owner->password)) {
            if ($this->authenticator->verifyCode($owner->mfa_secret_code, $request->input('verification_code'), 2)) {
                Auth::guard('owner')->login($owner, $request->boolean('remember'));
                $request->session()->regenerate();

                return response()->json([
                    'status' => 'OK',
                    'message' => 'Authentication successful.',
                    'redirect' => route('owner.dashboard'),
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
     * Generate Owner MFA Setup QR code.
     */
    public function generate(Request $request)
    {
        $owner = Auth::guard('owner')->user();
        if (!$owner) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $secret = $this->authenticator->createSecret();
        $appName = config('app.name', 'Car Rental System');
        $qrCodeUrl = $this->authenticator->getQRCodeGoogleUrl($owner->email, $secret, $appName . ' Owner');

        return response()->json([
            'status' => 'OK',
            'account' => $owner->email,
            'secret_key' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'image_url' => $qrCodeUrl,
        ]);
    }

    public function getMfaAuthenticatorCode(Request $request)
    {
        return $this->generate($request);
    }

    /**
     * Activate MFA for Owner.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'secret_key' => 'required|string',
            'verification_code' => 'required|string',
        ]);

        $owner = Auth::guard('owner')->user();
        if (!$owner) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $secret = $request->input('secret_key');
        $code = $request->input('verification_code');
        $imageUrl = $request->input('image_url') ?: $request->input('qr_code_url');

        if ($this->authenticator->verifyCode($secret, $code, 2)) {
            $owner->is_mfa_enabled = true;
            $owner->mfa_secret_code = $secret;
            $owner->mfa_authentication_image = $imageUrl;
            $owner->save();

            return response()->json([
                'status' => 'OK',
                'message' => 'Owner MFA successfully enabled.',
            ]);
        }

        return response()->json([
            'status' => 'ERROR',
            'message' => 'Invalid verification code. Please make sure the code matches your Authenticator app.',
            'errors' => 'Invalid verification code.',
        ], 422);
    }

    public function activateMfaAuthenticator(Request $request)
    {
        return $this->activate($request);
    }

    /**
     * Deactivate MFA for Owner.
     */
    public function deactivate(Request $request)
    {
        $owner = Auth::guard('owner')->user();
        if (!$owner) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $owner->is_mfa_enabled = false;
        if (!$owner->is_email_authentication_enabled) {
            $owner->mfa_secret_code = null;
        }
        $owner->mfa_authentication_image = null;
        $owner->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Owner MFA successfully disabled.',
        ]);
    }

    public function deactivateMfaAuthenticator(Request $request)
    {
        return $this->deactivate($request);
    }

    public function activateEmailAuthenticator()
    {
        $owner = Auth::guard('owner')->user();
        if (!$owner) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }
        $owner->is_email_authentication_enabled = true;
        $owner->save();

        return response()->json(['status' => 'OK', 'message' => 'Email authentication enabled.']);
    }

    public function deactivateEmailAuthenticator()
    {
        $owner = Auth::guard('owner')->user();
        if (!$owner) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }
        $owner->is_email_authentication_enabled = false;
        $owner->save();

        return response()->json(['status' => 'OK', 'message' => 'Email authentication disabled.']);
    }
}
