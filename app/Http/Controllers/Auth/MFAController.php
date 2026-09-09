<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Authenticator\Authenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MFAController extends Controller
{
    protected Authenticator $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    protected function getActiveUser()
    {
        return Auth::guard('admin')->user()
            ?? Auth::guard('owner')->user()
            ?? Auth::guard('customer')->user()
            ?? Auth::user();
    }

    public function getMfaAuthenticatorCode(Request $request)
    {
        $user = $this->getActiveUser();
        if (!$user) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $secret = $user->mfa_secret_code ?: $this->authenticator->createSecret();
        $appName = config('app.name', 'CarRentalSystem');
        $qrCodeUrl = $this->authenticator->getQRCodeGoogleUrl($user->email ?? 'user', $secret, $appName);

        return response()->json([
            'status' => 'OK',
            'secret_key' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    public function activateMfaAuthenticator(Request $request)
    {
        $request->validate([
            'secret_key' => 'required|string',
            'verification_code' => 'required|string',
        ]);

        $user = $this->getActiveUser();
        if (!$user) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $secret = $request->input('secret_key');
        $code = $request->input('verification_code');

        if ($this->authenticator->verifyCode($secret, $code, 2)) {
            $user->is_mfa_enabled = true;
            $user->mfa_secret_code = $secret;
            $user->mfa_authentication_image = $request->input('image_url');
            $user->save();

            return response()->json(['status' => 'OK', 'message' => 'MFA successfully activated.']);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Invalid verification code.'], 422);
    }

    public function deactivateMfaAuthenticator(Request $request)
    {
        $user = $this->getActiveUser();
        if (!$user) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $user->is_mfa_enabled = false;
        $user->mfa_secret_code = null;
        $user->mfa_authentication_image = null;
        $user->save();

        return response()->json(['status' => 'OK', 'message' => 'MFA deactivated successfully.']);
    }

    public function verifyMfaVerificationCode(Request $request)
    {
        $request->validate([
            'guard' => 'nullable|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'verification_code' => 'required|string',
        ]);

        $guard = $request->input('guard', 'admin');
        $modelClass = match ($guard) {
            'owner' => \App\Models\Owner::class,
            'customer' => \App\Models\Customer::class,
            default => \App\Models\Admin::class,
        };

        $user = $modelClass::where('email', $request->input('email'))->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password)) {
            if ($this->authenticator->verifyCode($user->mfa_secret_code, $request->input('verification_code'), 2)) {
                Auth::guard($guard)->login($user);
                $request->session()->regenerate();
                return response()->json(['status' => 'OK', 'data' => $user]);
            }
            return response()->json(['status' => 'ERROR', 'errors' => 'The MFA verification code is invalid.'], 422);
        }

        return response()->json(['status' => 'ERROR', 'errors' => 'Invalid credentials.'], 401);
    }
}
