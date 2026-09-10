<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Display the owner password reset view.
     */
    public function create(Request $request, ?string $token = null): Response
    {
        return Inertia::render('owner/auth/ResetPassword', [
            'token' => $token ?? $request->route('token') ?? '',
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Handle an incoming new password request for owner.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $res = PasswordResetService::resetPassword(
            $request->input('email'),
            $request->input('token'),
            $request->input('password'),
            'owner'
        );

        if ($res['status'] === 'success') {
            return redirect()->route('owner.login')->with('status', $res['message']);
        }

        return back()->withErrors(['email' => $res['message']]);
    }
}
