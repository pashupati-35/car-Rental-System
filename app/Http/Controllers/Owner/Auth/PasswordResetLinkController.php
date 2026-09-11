<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the owner password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('owner/auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $res = PasswordResetService::sendResetLink($request->input('email'), 'owner');

        if ($res['status'] === 'success') {
            return back()->with('status', $res['message']);
        }

        return back()->withErrors(['email' => $res['message']]);
    }
}
