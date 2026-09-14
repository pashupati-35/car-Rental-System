<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureDomainAccess
{
    /**
     * Handle domain-based routing enforcement between the dedicated Admin Portal
     * (portal.* subdomain) and the main application (APP_URL).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $isPortal = str_starts_with($host, 'portal.');

        // Skip internal assets and debugging routes
        if ($request->is('_ignition/*', 'sanctum/*', 'up', 'build/*', '@vite/*', 'storage/*', 'vendor/*')) {
            return $next($request);
        }

        // 1. DEDICATED ADMIN PORTAL SUBDOMAIN (e.g. portal.carrental.local)
        if ($isPortal) {
            $isAllowedOnPortal = $request->is(
                'admin',
                'admin/*',
                'login',
                'logout',
                'dashboard',
                'cms',
                'cms/*',
                'forgot-password',
                'reset-password',
                'reset-password/*',
                'mfa',
                'mfa/*',
                'api/admin/*',
            ) || $request->path() === '/';

            if (! $isAllowedOnPortal) {
                // Non-admin routes (owner, customer, public site) belong to the main app URL
                $mainAppBaseUrl = config('app.url') 
                    ? rtrim(config('app.url'), '/') 
                    : ($request->getScheme() . '://' . preg_replace('/^portal\./', '', $host) . (($request->getPort() && ! in_array($request->getPort(), [80, 443])) ? ':' . $request->getPort() : ''));

                $targetUrl = $mainAppBaseUrl . '/' . ltrim($request->getRequestUri(), '/');

                if ($request->header('X-Inertia')) {
                    return Inertia::location($targetUrl);
                }

                return redirect()->to($targetUrl);
            }
        } 
        // 2. MAIN APPLICATION DOMAIN (e.g. carrental.local)
        else {
            // Admin portal routes should only be accessed on the portal subdomain
            if ($request->is('admin', 'admin/*')) {
                // Only redirect if host contains domain parts (e.g. has '.' or matches APP_URL domain)
                $portSuffix = ($request->getPort() && ! in_array($request->getPort(), [80, 443])) ? ':' . $request->getPort() : '';
                $portalHost = 'portal.' . $host;
                $portalBaseUrl = $request->getScheme() . '://' . $portalHost . $portSuffix;
                $targetUrl = $portalBaseUrl . '/' . ltrim($request->getRequestUri(), '/');

                if ($request->header('X-Inertia')) {
                    return Inertia::location($targetUrl);
                }

                return redirect()->to($targetUrl);
            }
        }

        return $next($request);
    }
}
