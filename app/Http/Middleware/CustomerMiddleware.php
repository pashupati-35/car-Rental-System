<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        Auth::shouldUse('customer');
        if (Auth::guard('customer')->check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'UNAUTHORIZED',
                'message' => 'Session expired or unauthorized.',
            ], 401);
        }

        return redirect()->route('customer.login');
    }
}
