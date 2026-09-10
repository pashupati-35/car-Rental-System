<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the admin's profile page.
     */
    public function edit(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $admin,
            ]);
        }

        return Inertia::render('admin/Profile', [
            'user' => $admin,
        ]);
    }

    /**
     * Update the admin's profile information.
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'contact_number' => 'nullable|string|max:25',
            'address' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:100',
            'avatar' => 'nullable|string',
        ]);

        $admin->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Profile details updated successfully.',
                'user' => $admin,
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the admin password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password:admin',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $admin = Auth::guard('admin')->user();
        $admin->password = Hash::make($request->input('password'));
        $admin->save();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Password updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    /**
     * Delete the admin's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password:admin'],
        ]);

        $admin = Auth::guard('admin')->user();

        Auth::guard('admin')->logout();
        $admin->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/admin/login');
    }
}

