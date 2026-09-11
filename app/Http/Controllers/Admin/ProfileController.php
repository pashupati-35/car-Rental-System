<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Profile\UpdateProfileRequest;
use App\Http\Resources\AdminResource;
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
        $adminResource = $admin ? (new AdminResource($admin))->resolve() : null;

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $adminResource,
            ]);
        }

        return Inertia::render('admin/Profile', [
            'user' => $adminResource,
        ]);
    }

    /**
     * Display the admin's security & MFA page.
     */
    public function security(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $adminResource = $admin ? (new AdminResource($admin))->resolve() : null;

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $adminResource,
            ]);
        }

        return Inertia::render('admin/Security', [
            'user' => $adminResource,
        ]);
    }

    /**
     * Update the admin's profile information.
     */
    public function update(UpdateProfileRequest $request)
    {
        $admin = Auth::guard('admin')->user();
        $data = $request->validated();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $fileName = time().'_admin_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/admin'), $fileName);
            $data['image'] = 'uploads/admin/'.$fileName;
            $data['avatar'] = 'uploads/admin/'.$fileName;
        }

        if (empty($data['name']) && (! empty($data['first_name']) || ! empty($data['last_name']))) {
            $data['name'] = trim(($data['first_name'] ?? '').' '.($data['middle_name'] ?? '').' '.($data['last_name'] ?? ''));
        }

        if (empty($data['contact_number']) && ! empty($data['mobile'])) {
            $data['contact_number'] = $data['mobile'];
        }

        if (empty($data['date_of_birth'])) {
            $data['date_of_birth'] = null;
        }

        $admin->update($data);
        $admin = $admin->fresh();
        $adminResource = (new AdminResource($admin))->resolve();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Admin profile details updated successfully.',
                'user' => $adminResource,
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the admin's theme style preference in the database.
     */
    public function updateThemeStyle(Request $request)
    {
        $request->validate([
            'theme_style' => 'required|string|in:light,dark,midnight,system',
        ]);

        $admin = Auth::guard('admin')->user();
        if ($admin) {
            $admin->theme_style = $request->input('theme_style');
            $admin->save();
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Theme style preference saved to database successfully.',
            'theme_style' => $admin ? $admin->theme_style : $request->input('theme_style'),
        ]);
    }

    /**
     * Update the admin password (without requiring old password).
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
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

        $request->session()->forget('admin_impersonating');
        $request->session()->regenerateToken();

        return redirect()->to('/admin/login');
    }
}
