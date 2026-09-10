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

        $secret = $customer->mfa_secret_code ?: $this->authenticator->createSecret();
        $appName = config('app.name', 'CarRentalSystem') . ' Customer';
        $qrCodeUrl = $this->authenticator->getQRCodeGoogleUrl($customer->email, $secret, $appName);

        return response()->json([
            'status' => 'OK',
            'secret_key' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
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

        if ($this->authenticator->verifyCode($secret, $code, 2)) {
            $customer->is_mfa_enabled = true;
            $customer->mfa_secret_code = $secret;
            $customer->save();

            return response()->json([
                'status' => 'OK',
                'message' => 'Customer MFA successfully enabled.',
            ]);
        }

        return response()->json([
            'status' => 'ERROR',
            'message' => 'Invalid verification code.',
        ], 422);
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
        $customer->mfa_secret_code = null;
        $customer->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Customer MFA successfully disabled.',
        ]);
    }
}
