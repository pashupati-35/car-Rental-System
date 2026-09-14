<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Auth\RegisterCustomerRequest;
use App\Services\CustomerService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredCustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('customer/auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterCustomerRequest $request): RedirectResponse
    {
        $customer = $this->customerService->registerCustomer($request->validated());

        event(new Registered($customer));

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard');
    }
}
