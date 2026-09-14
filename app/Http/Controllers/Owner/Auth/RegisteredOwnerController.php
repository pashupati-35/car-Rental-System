<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Auth\RegisterOwnerRequest;
use App\Services\OwnerService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredOwnerController extends Controller
{
    public function __construct(
        protected OwnerService $ownerService
    ) {}

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('owner/auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterOwnerRequest $request): RedirectResponse
    {
        $owner = $this->ownerService->registerOwner($request->validated());

        event(new Registered($owner));

        Auth::guard('owner')->login($owner);

        return redirect()->route('owner.dashboard');
    }
}
