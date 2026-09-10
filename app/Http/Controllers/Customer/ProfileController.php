<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the customer's profile form.
     */
    public function edit(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $customer,
            ]);
        }

        return Inertia::render('customer/Profile', [
            'user' => $customer,
        ]);
    }

    /**
     * Display the customer's security & MFA form.
     */
    public function security(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $customer,
            ]);
        }

        return Inertia::render('customer/Security', [
            'user' => $customer,
        ]);
    }

    /**
     * Update the customer's profile information.
     */
    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Profile updated successfully.',
                'user' => $customer,
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the customer's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password:customer'],
        ]);

        $customer = Auth::guard('customer')->user();

        Auth::guard('customer')->logout();
        $customer->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/customer/login');
    }
}

