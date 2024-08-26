<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
//    public function update(Request $request): RedirectResponse
//    {
//        $validated = $request->validateWithBag('updatePassword', [
//            'current_password' => ['required', 'current_password'],
//            'password' => ['required', Password::defaults(), 'confirmed'],
//        ]);
//
//        $request->guard('customer')->user()->update([
//            'password' => Hash::make($validated['password']),
//        ]);
//
//        return back('customer.dashboard')->with('status', 'password-updated');
//    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::guard('customer')->user();

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('status', 'password-updated');
    }
}
