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
     * Check if Customer has MFA enabled before logging in.
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $request->input('email'))->first();

        if ($customer && Hash::check($request->input('password'), $customer->password)) {
            return response()->json([
                'status' => 'OK',
                'data' => [
                    'id' => $customer->id,
                    'email' => $customer->email,
                    'name' => $customer->name,
                    'is_mfa_enabled' => (bool)$customer->is_mfa_enabled,
                ],
            ]);
        }

        return response()->json([
            'status' => 'ERROR',
            'errors' => 'Invalid email or password.',
        ], 401);
    }

    /**
     * Verify Customer MFA code and login.
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
            if ($this->authenticator->verifyCode($customer->mfa_secret_code, $request->input('verification_code'), 2)) {
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
                'errors' => 'The MFA verification code is invalid.',
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
}
