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
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:100|unique:owners,username,'.$owner->id,
            'email' => 'required|email|max:255|unique:owners,email,'.$owner->id,
            'contact_number' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
            'nationality' => 'nullable|string|max:100',
            'citizenship_number' => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:50',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_relationship' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time().'_owner_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/owner'), $fileName);
            $validated['image'] = 'uploads/owner/'.$fileName;
        }

        // Auto compose full_name if first_name / last_name provided
        if (empty($validated['full_name']) && (! empty($validated['first_name']) || ! empty($validated['last_name']))) {
            $validated['full_name'] = trim(($validated['first_name'] ?? '').' '.($validated['middle_name'] ?? '').' '.($validated['last_name'] ?? ''));
        }

        if (empty($validated['contact_number']) && ! empty($validated['mobile'])) {
            $validated['contact_number'] = $validated['mobile'];
        }

        if (empty($validated['date_of_birth'])) {
            $validated['date_of_birth'] = null;
        }

        $owner->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Fleet Owner profile updated successfully.',
                'user' => $owner->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Fleet Owner profile updated successfully.');
    }

    /**
     * Update the owner's preferred theme style.
     */
    public function updateThemeStyle(Request $request)
    {
        $request->validate([
            'theme_style' => ['required', 'string', 'in:dark,midnight,light,system'],
        ]);

        $owner = Auth::guard('owner')->user();
        if ($owner) {
            $owner->theme_style = $request->input('theme_style');
            $owner->save();
        }

        return response()->json([
            'status' => 'OK',
            'theme_style' => $owner ? $owner->theme_style : $request->input('theme_style'),
        ]);
    }

    /**
     * Update the owner's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $owner = Auth::guard('owner')->user();
        $owner->password = \Illuminate\Support\Facades\Hash::make($request->input('password'));
        $owner->save();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Password updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Password updated successfully.');
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
