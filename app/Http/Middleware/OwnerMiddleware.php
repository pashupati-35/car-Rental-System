<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        Auth::shouldUse('owner');
        if (Auth::guard('owner')->check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'UNAUTHORIZED',
                'message' => 'Session expired or unauthorized.',
            ], 401);
        }

        return redirect()->route('owner.login');
    }
}
