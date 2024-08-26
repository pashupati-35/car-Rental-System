<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Customer;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();

        // Find or create a user
        $customer = Customer::firstOrCreate(
            ['email' => $user->getEmail()],
            ['name' => $user->getName(), 'password' => bcrypt('password')] // Replace with a secure password or handle appropriately
        );

        // Log in the user
        Auth::guard('customer')->login($customer);

        return redirect()->intended(route('customer.dashboard'));
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $user = Socialite::driver('facebook')->user();

        // Find or create a user
        $customer = Customer::firstOrCreate(
            ['email' => $user->getEmail()],
            ['name' => $user->getName(), 'password' => bcrypt('password')] // Replace with a secure password or handle appropriately
        );

        // Log in the user
        Auth::guard('customer')->login($customer);

        return redirect()->intended(route('customer.dashboard'));
    }
}
