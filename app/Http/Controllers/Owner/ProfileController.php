<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the owner's profile form.
     */
    public function edit(Request $request)
    {
        $owner = Auth::guard('owner')->user();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $owner,
            ]);
        }

        return Inertia::render('owner/Profile', [
            'user' => $owner,
        ]);
    }

    /**
     * Display the owner's security & MFA form.
     */
    public function security(Request $request)
    {
        $owner = Auth::guard('owner')->user();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $owner,
            ]);
        }

        return Inertia::render('owner/Security', [
            'user' => $owner,
        ]);
    }

    /**
     * Update the owner's profile information.
     */
    public function update(Request $request)
    {
        $owner = Auth::guard('owner')->user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:owners,email,'.$owner->id,
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $owner->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Profile updated successfully.',
                'user' => $owner,
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the owner's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password:owner'],
        ]);

        $owner = Auth::guard('owner')->user();

        Auth::guard('owner')->logout();
        $owner->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/owner/login');
    }
}
