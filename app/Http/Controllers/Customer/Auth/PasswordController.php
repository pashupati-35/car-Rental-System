<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the customer's password.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password:customer',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $customer = Auth::guard('customer')->user();
        $customer->password = Hash::make($request->input('password'));
        $customer->save();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Password updated successfully.',
            ]);
        }

        return back()->with('success', 'Password updated successfully.');
    }
}

