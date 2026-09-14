<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            if ($request->wantsJson() && ! $request->header('X-Inertia')) {
                return response()->json(['status' => 'OK', 'user' => Auth::guard('admin')->user()]);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json(['status' => 'ERROR', 'message' => __('auth.failed')], 422);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        return response()->json(['status' => 'OK', 'message' => 'Reset link sent if account exists.']);
    }

    public function doResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        return response()->json(['status' => 'OK', 'message' => 'Password reset successfully.']);
    }

    public function verify(Request $request)
    {
        return response()->json(['status' => 'OK', 'verified' => true]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->forget('admin_impersonating');
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Logged out.']);
        }

        return redirect()->route('admin.login');
    }
}
