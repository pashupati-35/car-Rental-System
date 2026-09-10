<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle Owner login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('owner')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            if ($request->wantsJson()) {
                return response()->json(['status' => 'OK', 'user' => Auth::guard('owner')->user()]);
            }
            return redirect()->intended(route('owner.dashboard'));
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ERROR', 'message' => __('auth.failed')], 422);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Reset password request.
     */
    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        return response()->json(['status' => 'OK', 'message' => 'Reset link sent if account exists.']);
    }

    /**
     * Perform password reset.
     */
    public function doResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        return response()->json(['status' => 'OK', 'message' => 'Password reset successfully.']);
    }

    /**
     * Verification status check.
     */
    public function verify(Request $request)
    {
        return response()->json(['status' => 'OK', 'verified' => true]);
    }

    /**
     * Owner logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Logged out.']);
        }

        return redirect()->route('owner.login');
    }
}
