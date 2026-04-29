<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\OwnerRegisterRequest;
use App\Models\Owner;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredOwnerController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('owner.auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(OwnerRegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $owner = Owner::create([
            'full_name' => $validated['full_name'],
            'contact_number' => $validated['contact_number'],
            'address' => $validated['address'],
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($owner));

        Auth::guard('owner')->login($owner);

        return redirect()->route('owner.dashboard');
    }
}
