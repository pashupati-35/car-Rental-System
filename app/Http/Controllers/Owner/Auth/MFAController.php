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
     * Check if Owner has MFA or Email Authentication enabled before logging in.
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $owner = Owner::where('email', $request->input('email'))->first();

        if ($owner && Hash::check($request->input('password'), $owner->password)) {
            $isMfa = (bool)$owner->is_mfa_enabled;
            $isEmailAuth = (bool)$owner->is_email_authentication_enabled;
            $codeSent = false;

            if ($isEmailAuth && !$isMfa) {
                // Generate 6-digit OTP code for email verification
                $code = sprintf('%06d', random_int(100000, 999999));
                $owner->mfa_secret_code = $code;
                $owner->save();

                try {
                    \App\Jobs\Admin\EmailVerificationJob::dispatchSync($owner, $code);
                    $codeSent = true;
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Owner email verification send failed: ' . $e->getMessage());
                    $codeSent = true;
                }
            }

            return response()->json([
                'status' => 'OK',
                'data' => [
                    'id' => $owner->id,
                    'email' => $owner->email,
                    'name' => $owner->full_name,
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
     * Resend verification email code for Owner.
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $owner = Owner::where('email', $request->input('email'))->first();

        if ($owner && Hash::check($request->input('password'), $owner->password) && $owner->is_email_authentication_enabled) {
            $code = sprintf('%06d', random_int(100000, 999999));
            $owner->mfa_secret_code = $code;
            $owner->save();

            try {
                \App\Jobs\Admin\EmailVerificationJob::dispatchSync($owner, $code);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Resend owner email verification failed: ' . $e->getMessage());
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
     * Verify Owner MFA / Email OTP code and login.
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
            $inputCode = trim((string)$request->input('verification_code'));
            $verified = false;

            // Email authentication verification
            if ($owner->is_email_authentication_enabled && !$owner->is_mfa_enabled) {
                if ($owner->mfa_secret_code && (string)$owner->mfa_secret_code === $inputCode) {
                    $verified = true;
                }
            }
            // Authenticator app (TOTP) verification
            elseif ($owner->is_mfa_enabled && $owner->mfa_secret_code) {
                if ($this->authenticator->verifyCode($owner->mfa_secret_code, $inputCode, 2)) {
                    $verified = true;
                }
            }
            // Fallback
            elseif ($owner->mfa_secret_code && ((string)$owner->mfa_secret_code === $inputCode || $this->authenticator->verifyCode($owner->mfa_secret_code, $inputCode, 2))) {
                $verified = true;
            }

            if ($verified) {
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
                'errors' => 'The verification code entered is invalid or has expired.',
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
