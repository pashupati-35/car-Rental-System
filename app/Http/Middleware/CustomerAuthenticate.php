<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('customer')->check()) {
            return $next($request);
        }

        return redirect()->route('customer.login'); // Adjust to your customer login route
    }
}
