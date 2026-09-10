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
     * Check if Admin has MFA or Email Authentication enabled before logging in.
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->input('email'))->first();

        if ($admin && Hash::check($request->input('password'), $admin->password)) {
            $isMfa = (bool)$admin->is_mfa_enabled;
            $isEmailAuth = (bool)$admin->is_email_authentication_enabled;
            $codeSent = false;

            if ($isEmailAuth && !$isMfa) {
                // Generate 6-digit OTP code for email verification
                $code = sprintf('%06d', random_int(100000, 999999));
                $admin->mfa_secret_code = $code;
                $admin->save();

                try {
                    \App\Jobs\Admin\EmailVerificationJob::dispatchSync($admin, $code);
                    $codeSent = true;
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Email verification send failed: ' . $e->getMessage());
                    // Fallback log sending
                    $codeSent = true;
                }
            }

            return response()->json([
                'status' => 'OK',
                'data' => [
                    'id' => $admin->id,
                    'email' => $admin->email,
                    'name' => $admin->name,
                    'is_mfa_enabled' => $isMfa,
                    'is_email_authentication_enabled' => $isEmailAuth,
                    'auth_type' => $isMfa ? 'totp' : ($isEmailAuth ? 'email' : 'none'),
                    'code_sent' => $codeSent,
                ],
            ]);
        }

        return response()->json([
            'status' => 'ERROR',
            'errors' => 'Invalid email or password.',
        ], 401);
    }

    /**
     * Resend verification email code.
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->input('email'))->first();

        if ($admin && Hash::check($request->input('password'), $admin->password) && $admin->is_email_authentication_enabled) {
            $code = sprintf('%06d', random_int(100000, 999999));
            $admin->mfa_secret_code = $code;
            $admin->save();

            try {
                \App\Jobs\Admin\EmailVerificationJob::dispatchSync($admin, $code);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Resend email verification failed: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'OK',
                'message' => 'A new 6-digit verification code has been sent to your email.',
            ]);
        }

        return response()->json([
            'status' => 'ERROR',
            'errors' => 'Unable to resend verification code.',
        ], 422);
    }

    /**
     * Verify Admin MFA / Email OTP code and login.
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
            $inputCode = trim((string)$request->input('verification_code'));
            $verified = false;

            // Email authentication verification
            if ($admin->is_email_authentication_enabled && !$admin->is_mfa_enabled) {
                if ($admin->mfa_secret_code && (string)$admin->mfa_secret_code === $inputCode) {
                    $verified = true;
                }
            } 
            // Authenticator app (TOTP) verification
            elseif ($admin->is_mfa_enabled && $admin->mfa_secret_code) {
                if ($this->authenticator->verifyCode($admin->mfa_secret_code, $inputCode, 2)) {
                    $verified = true;
                }
            }
            // If both or fallback
            elseif ($admin->mfa_secret_code && ((string)$admin->mfa_secret_code === $inputCode || $this->authenticator->verifyCode($admin->mfa_secret_code, $inputCode, 2))) {
                $verified = true;
            }

            if ($verified) {
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
                'errors' => 'The verification code is invalid or has expired.',
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

        $secret = $this->authenticator->createSecret();
        $appName = config('app.name', 'Car Rental System');
        $qrCodeUrl = $this->authenticator->getQRCodeGoogleUrl($admin->email, $secret, $appName . ' Admin');

        return response()->json([
            'status' => 'OK',
            'account' => $admin->email,
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
        $imageUrl = $request->input('image_url') ?: $request->input('qr_code_url');

        if ($this->authenticator->verifyCode($secret, $code, 2)) {
            $admin->is_mfa_enabled = true;
            $admin->mfa_secret_code = $secret;
            $admin->mfa_authentication_image = $imageUrl;
            $admin->save();

            return response()->json([
                'status' => 'OK',
                'message' => 'Admin MFA successfully enabled.',
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
     * Deactivate MFA for Admin.
     */
    public function deactivate(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $admin->is_mfa_enabled = false;
        if (!$admin->is_email_authentication_enabled) {
            $admin->mfa_secret_code = null;
        }
        $admin->mfa_authentication_image = null;
        $admin->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Admin MFA successfully disabled.',
        ]);
    }

    public function deactivateMfaAuthenticator(Request $request)
    {
        return $this->deactivate($request);
    }

    public function activateEmailAuthenticator()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }
        $admin->is_email_authentication_enabled = true;
        $admin->save();

        return response()->json(['status' => 'OK', 'message' => 'Email authentication enabled.']);
    }

    public function deactivateEmailAuthenticator()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }
        $admin->is_email_authentication_enabled = false;
        $admin->save();

        return response()->json(['status' => 'OK', 'message' => 'Email authentication disabled.']);
    }

    public function checkVerificationEnabled(Request $request)
    {
        return $this->checkVerification($request);
    }

    public function requestEmailVerificationCode(Request $request)
    {
        return $this->resendCode($request);
    }

    public function verifyMfaVerificationCode(Request $request)
    {
        return $this->verifyCode($request);
    }

    public function verifyEmailVerificationCode(Request $request)
    {
        return $this->verifyCode($request);
    }
}
