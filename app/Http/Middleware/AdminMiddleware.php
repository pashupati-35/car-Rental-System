<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        Auth::shouldUse('admin');
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'UNAUTHORIZED',
                'message' => 'Session expired or unauthorized.',
            ], 401);
        }

        return redirect()->route('admin.login');
    }
}
