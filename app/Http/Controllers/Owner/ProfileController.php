<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Profile\UpdatePasswordRequest;
use App\Http\Requests\Owner\Profile\UpdateProfileRequest;
use App\Http\Requests\Owner\Profile\UpdateThemeStyleRequest;
use App\Http\Resources\OwnerResource;
use App\Services\OwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function __construct(
        protected OwnerService $ownerService
    ) {}

    /**
     * Display the owner's profile form.
     */
    public function edit(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $owner = $this->ownerService->getOwnerProfile($ownerId);
        $ownerResource = $owner ? (new OwnerResource($owner))->resolve() : null;

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $ownerResource,
            ]);
        }

        return Inertia::render('owner/Profile', [
            'user' => $ownerResource,
        ]);
    }

    /**
     * Display the owner's security & MFA form.
     */
    public function security(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $owner = $this->ownerService->getOwnerProfile($ownerId);
        $ownerResource = $owner ? (new OwnerResource($owner))->resolve() : null;

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $ownerResource,
            ]);
        }

        return Inertia::render('owner/Security', [
            'user' => $ownerResource,
        ]);
    }

    /**
     * Update the owner's profile information.
     */
    public function update(UpdateProfileRequest $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $validated = $request->validated();

        $owner = $this->ownerService->updateOwnerProfile($ownerId, $validated, $request->file('image'));
        $ownerResource = (new OwnerResource($owner))->resolve();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Fleet Owner profile updated successfully.',
                'user' => $ownerResource,
            ]);
        }

        return redirect()->back()->with('success', 'Fleet Owner profile updated successfully.');
    }

    /**
     * Update the owner's preferred theme style.
     */
    public function updateThemeStyle(UpdateThemeStyleRequest $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $themeStyle = $this->ownerService->updateOwnerThemeStyle($ownerId, $request->validated('theme_style'));

        return response()->json([
            'status' => 'OK',
            'theme_style' => $themeStyle,
        ]);
    }

    /**
     * Update the owner's password.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $this->ownerService->updateOwnerPassword($ownerId, $request->validated('password'));

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

        $ownerId = Auth::guard('owner')->id();
        Auth::guard('owner')->logout();

        $this->ownerService->deleteOwnerAccount($ownerId);

        $request->session()->regenerateToken();

        return redirect()->to('/owner/login');
    }
}
