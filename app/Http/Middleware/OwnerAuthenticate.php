<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('owner')->check()) {
            return $next($request);
        }

        return redirect()->route('owner.login'); // Adjust to your owner login route
    }
}
