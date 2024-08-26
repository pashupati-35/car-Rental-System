<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OwnerLoginRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the owner's profile form.
     */
    public function edit(Request $request): View
    {
        return view('owner.profile.edit', [
            'user' => $request->user('owner'),
        ]);
    }

    /**
     * Update the owner's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user('owner')->fill($request->validated());

        if ($request->user('owner')->isDirty('email')) {
            $request->user('owner')->email_verified_at = null;
        }

        $request->user('owner')->save();

        return Redirect::route('owner.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the owner's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $owner = $request->user('owner');

        Auth::guard('owner')->logout();

        $owner->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

}
