<?php

namespace App\Http\Controllers\Crm\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\Admin\EmailVerificationJob;
use App\Models\Admin;
use App\Services\Authenticator\Authenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CrmAuthController extends Controller
{
    public function __construct(protected Authenticator $authenticator) {}

    /**
     * Display the CRM portal login view.
     */
    public function create()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('crm.dashboard');
        }

        return Inertia::render('crm/auth/Login', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle CRM login authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('crm.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Pre-login MFA verification check for CRM portal.
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->input('email'))->first();

        if ($admin && Hash::check($request->input('password'), $admin->password)) {
            $isMfa = (bool) $admin->is_mfa_enabled;
            $isEmailAuth = (bool) $admin->is_email_authentication_enabled;
            $codeSent = false;

            if ($isEmailAuth && ! $isMfa) {
                $code = sprintf('%06d', random_int(100000, 999999));
                $admin->mfa_secret_code = $code;
                $admin->save();

                try {
                    EmailVerificationJob::dispatchSync($admin, $code);
                    $codeSent = true;
                } catch (\Throwable $e) {
                    Log::warning('CRM Email verification send failed: '.$e->getMessage());
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
     * Resend verification email code for CRM.
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
                EmailVerificationJob::dispatchSync($admin, $code);
            } catch (\Throwable $e) {
                Log::warning('CRM Resend email verification failed: '.$e->getMessage());
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
     * Verify MFA code and authenticate into CRM portal.
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
            $inputCode = trim((string) $request->input('verification_code'));
            $verified = false;

            // Email authentication verification
            if ($admin->is_email_authentication_enabled && ! $admin->is_mfa_enabled) {
                if ($admin->mfa_secret_code && (string) $admin->mfa_secret_code === $inputCode) {
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
            elseif ($admin->mfa_secret_code && ((string) $admin->mfa_secret_code === $inputCode || $this->authenticator->verifyCode($admin->mfa_secret_code, $inputCode, 2))) {
                $verified = true;
            }

            if ($verified) {
                Auth::guard('admin')->login($admin, $request->boolean('remember'));
                $request->session()->regenerate();

                return response()->json([
                    'status' => 'OK',
                    'message' => 'Authentication successful.',
                    'redirect' => route('crm.dashboard'),
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
     * Destroy CRM authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('crm.login');
    }
}
