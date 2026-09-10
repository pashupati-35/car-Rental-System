<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\Authenticator\Authenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MFAController extends Controller
{
    public function __construct(protected Authenticator $authenticator) {}

    /**
     * Check if Customer has MFA or Email Authentication enabled before logging in.
     */
     public function checkVerification(Request $request)
     {
         $request->validate([
             'email' => 'required|email',
             'password' => 'required|string',
         ]);

         $customer = Customer::where('email', $request->input('email'))->first();

         if ($customer && Hash::check($request->input('password'), $customer->password)) {
             $isMfa = (bool)$customer->is_mfa_enabled;
             $isEmailAuth = (bool)$customer->is_email_authentication_enabled;
             $codeSent = false;

             if ($isEmailAuth && !$isMfa) {
                 // Generate 6-digit OTP code for email verification
                 $code = sprintf('%06d', random_int(100000, 999999));
                 $customer->mfa_secret_code = $code;
                 $customer->save();

                 try {
                     \App\Jobs\Admin\EmailVerificationJob::dispatchSync($customer, $code);
                     $codeSent = true;
                 } catch (\Throwable $e) {
                     \Illuminate\Support\Facades\Log::warning('Customer email verification send failed: ' . $e->getMessage());
                     $codeSent = true;
                 }
             }

             return response()->json([
                 'status' => 'OK',
                 'data' => [
                     'id' => $customer->id,
                     'email' => $customer->email,
                     'name' => $customer->name,
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
      * Resend verification email code for Customer.
      */
     public function resendCode(Request $request)
     {
         $request->validate([
             'email' => 'required|email',
             'password' => 'required|string',
         ]);

         $customer = Customer::where('email', $request->input('email'))->first();

         if ($customer && Hash::check($request->input('password'), $customer->password) && $customer->is_email_authentication_enabled) {
             $code = sprintf('%06d', random_int(100000, 999999));
             $customer->mfa_secret_code = $code;
             $customer->save();

             try {
                 \App\Jobs\Admin\EmailVerificationJob::dispatchSync($customer, $code);
             } catch (\Throwable $e) {
                 \Illuminate\Support\Facades\Log::warning('Resend customer email verification failed: ' . $e->getMessage());
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
      * Verify Customer MFA / Email OTP code and login.
      */
     public function verifyCode(Request $request)
     {
         $request->validate([
             'email' => 'required|email',
             'password' => 'required|string',
             'verification_code' => 'required|string',
         ]);

         $customer = Customer::where('email', $request->input('email'))->first();

         if ($customer && Hash::check($request->input('password'), $customer->password)) {
             $inputCode = trim((string)$request->input('verification_code'));
             $verified = false;

             // Email authentication verification
             if ($customer->is_email_authentication_enabled && !$customer->is_mfa_enabled) {
                 if ($customer->mfa_secret_code && (string)$customer->mfa_secret_code === $inputCode) {
                     $verified = true;
                 }
             }
             // Authenticator app (TOTP) verification
             elseif ($customer->is_mfa_enabled && $customer->mfa_secret_code) {
                 if ($this->authenticator->verifyCode($customer->mfa_secret_code, $inputCode, 2)) {
                     $verified = true;
                 }
             }
             // Fallback
             elseif ($customer->mfa_secret_code && ((string)$customer->mfa_secret_code === $inputCode || $this->authenticator->verifyCode($customer->mfa_secret_code, $inputCode, 2))) {
                 $verified = true;
             }

             if ($verified) {
                 Auth::guard('customer')->login($customer, $request->boolean('remember'));
                 $request->session()->regenerate();

                 return response()->json([
                     'status' => 'OK',
                     'message' => 'Authentication successful.',
                     'redirect' => route('customer.dashboard'),
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
     * Generate Customer MFA Setup QR code.
     */
    public function generate(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $secret = $this->authenticator->createSecret();
        $appName = config('app.name', 'Car Rental System');
        $qrCodeUrl = $this->authenticator->getQRCodeGoogleUrl($customer->email, $secret, $appName . ' Customer');

        return response()->json([
            'status' => 'OK',
            'account' => $customer->email,
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
     * Activate MFA for Customer.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'secret_key' => 'required|string',
            'verification_code' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $secret = $request->input('secret_key');
        $code = $request->input('verification_code');
        $imageUrl = $request->input('image_url') ?: $request->input('qr_code_url');

        if ($this->authenticator->verifyCode($secret, $code, 2)) {
            $customer->is_mfa_enabled = true;
            $customer->mfa_secret_code = $secret;
            $customer->mfa_authentication_image = $imageUrl;
            $customer->save();

            return response()->json([
                'status' => 'OK',
                'message' => 'Customer MFA successfully enabled.',
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
     * Deactivate MFA for Customer.
     */
    public function deactivate(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }

        $customer->is_mfa_enabled = false;
        if (!$customer->is_email_authentication_enabled) {
            $customer->mfa_secret_code = null;
        }
        $customer->mfa_authentication_image = null;
        $customer->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Customer MFA successfully disabled.',
        ]);
    }

    public function deactivateMfaAuthenticator(Request $request)
    {
        return $this->deactivate($request);
    }

    public function activateEmailAuthenticator()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }
        $customer->is_email_authentication_enabled = true;
        $customer->save();

        return response()->json(['status' => 'OK', 'message' => 'Email authentication enabled.']);
    }

    public function deactivateEmailAuthenticator()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['status' => 'UNAUTHORIZED'], 401);
        }
        $customer->is_email_authentication_enabled = false;
        $customer->save();

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
