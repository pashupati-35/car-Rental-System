<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Profile\UpdateCustomerPasswordRequest;
use App\Http\Requests\Customer\Profile\UpdateCustomerProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /**
     * Display the customer's profile form.
     */
    public function edit(Request $request)
    {
        $customerId = Auth::guard('customer')->id();
        $customer = $this->customerService->getCustomerProfile($customerId);
        $customerResource = $customer ? (new CustomerResource($customer))->resolve() : null;

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $customerResource,
            ]);
        }

        return Inertia::render('customer/Profile', [
            'user' => $customerResource,
        ]);
    }

    /**
     * Display the customer's security & MFA form.
     */
    public function security(Request $request)
    {
        $customerId = Auth::guard('customer')->id();
        $customer = $this->customerService->getCustomerProfile($customerId);
        $customerResource = $customer ? (new CustomerResource($customer))->resolve() : null;

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $customerResource,
            ]);
        }

        return Inertia::render('customer/Security', [
            'user' => $customerResource,
        ]);
    }

    /**
     * Update the customer's profile information.
     */
    public function update(UpdateCustomerProfileRequest $request)
    {
        $customerId = Auth::guard('customer')->id();
        $validated = $request->validated();

        $customer = $this->customerService->updateCustomerProfile(
            $customerId,
            $validated,
            $request->file('image'),
            $request->boolean('remove_image')
        );
        $customerResource = (new CustomerResource($customer))->resolve();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Profile updated successfully.',
                'user' => $customerResource,
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the customer's preferred theme style.
     */
    public function updateThemeStyle(Request $request)
    {
        $request->validate([
            'theme_style' => 'required|string|in:light,dark,midnight,system',
        ]);

        $customerId = Auth::guard('customer')->id();
        $themeStyle = $this->customerService->updateCustomerThemeStyle($customerId, $request->input('theme_style'));

        return response()->json([
            'status' => 'OK',
            'theme_style' => $themeStyle,
        ]);
    }

    /**
     * Update the customer's password.
     */
    public function updatePassword(UpdateCustomerPasswordRequest $request)
    {
        $customerId = Auth::guard('customer')->id();
        $this->customerService->updateCustomerPassword($customerId, $request->validated('password'));

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Password updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    /**
     * Delete the customer's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password:customer'],
        ]);

        $customerId = Auth::guard('customer')->id();
        Auth::guard('customer')->logout();

        $this->customerService->deleteCustomerAccount($customerId);

        $request->session()->regenerateToken();

        return redirect()->to('/customer/login');
    }
}
