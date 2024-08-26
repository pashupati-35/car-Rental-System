<?php
//
//namespace App\Http\Controllers\Admin\Auth;
//
//use App\Http\Controllers\Controller;
//use App\Http\Requests\Auth\AdminLoginRequest;
//use Illuminate\Http\RedirectResponse;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\View\View;
//
//class AuthenticatedSessionController extends Controller
//{
//    /**
//     * Display the login view.
//     */
//    public function create(): View
//    {
//        return view('admin.auth.login');
//    }
//
//    /**
//     * Handle an incoming authentication request.
//     */
//    public function store(AdminLoginRequest $request): RedirectResponse
//    {
//        $request->authenticate();
//
//        $request->session()->regenerate();
//
//        return redirect()->intended(route('admin.dashboard', absolute: false));
//    }
//
//    /**
//     * Destroy an authenticated session.
//     */
//    public function destroy(Request $request): RedirectResponse
//    {
//        Auth::guard('admin')->logout();
//
//        $request->session()->invalidate();
//
//        $request->session()->regenerateToken();
//
//        return redirect('/');
//    }
//}



namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('home'));
    }
    public function dashboard()
    {
        if (Auth::guard('admin')->check()) {
            return view('admin.dashboard');
        }

        return redirect(route('admin.login'))->with('error', 'Please login to access the dashboard.');
    }
}

