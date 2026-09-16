<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureDomainAccess
{
    /**
     * Handle domain-based routing enforcement between:
     * 1. Dedicated CRM Portal (portal.crmcarrental.local / portal.crmcarrental.com)
     * 2. Dedicated Admin Portal (portal.carrental.local / portal.carrental.com)
     * 3. Main Customer Booking Application (carrental.local / crmcarrental.local)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Skip internal assets and debugging routes
        if ($request->is('_ignition/*', 'sanctum/*', 'up', 'build/*', '@vite/*', 'storage/*', 'vendor/*')) {
            return $next($request);
        }

        $isCrmPortal = str_starts_with($host, 'portal.crmcarrental.');
        $isAdminPortal = str_starts_with($host, 'portal.carrental.') || (str_starts_with($host, 'portal.') && ! $isCrmPortal);

        // 1. DEDICATED CRM PORTAL SUBDOMAIN (e.g. portal.crmcarrental.local / portal.crmcarrental.com)
        if ($isCrmPortal) {
            $isAllowedOnCrm = $request->is(
                'crm',
                'crm/*',
                'admin/crm',
                'admin/crm/*',
                'login',
                'logout',
                'dashboard',
                'mfa',
                'mfa/*',
                'api/*',
            ) || $request->path() === '/';

            if (! $isAllowedOnCrm) {
                // If user tried to access non-CRM page on CRM portal, redirect to CRM dashboard or login
                $targetUrl = auth()->guard('admin')->check() ? '/crm/dashboard' : '/crm/login';

                if ($request->header('X-Inertia')) {
                    return Inertia::location($targetUrl);
                }

                return redirect()->to($targetUrl);
            }

            return $next($request);
        }

        // 2. DEDICATED ADMIN PORTAL SUBDOMAIN (e.g. portal.carrental.local)
        if ($isAdminPortal) {
            // CRM routes should not be accessed on the old Admin portal - redirect to CRM portal
            if ($request->is('admin/crm', 'admin/crm/*', 'crm', 'crm/*')) {
                $portSuffix = ($request->getPort() && ! in_array($request->getPort(), [80, 443])) ? ':' . $request->getPort() : '';
                $crmHost = str_replace('portal.carrental.', 'portal.crmcarrental.', $host);
                if (! str_starts_with($crmHost, 'portal.crmcarrental.')) {
                    $crmHost = 'portal.crmcarrental.local';
                }
                $crmBaseUrl = $request->getScheme() . '://' . $crmHost . $portSuffix;
                $targetUrl = $crmBaseUrl . '/' . ltrim($request->getRequestUri(), '/');

                if ($request->header('X-Inertia')) {
                    return Inertia::location($targetUrl);
                }

                return redirect()->to($targetUrl);
            }

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
                $portSuffix = ($request->getPort() && ! in_array($request->getPort(), [80, 443])) ? ':' . $request->getPort() : '';
                $mainAppBaseUrl = $request->getScheme() . '://' . preg_replace('/^portal\./', '', $host) . $portSuffix;
                $targetUrl = $mainAppBaseUrl . '/' . ltrim($request->getRequestUri(), '/');

                if ($request->header('X-Inertia')) {
                    return Inertia::location($targetUrl);
                }

                return redirect()->to($targetUrl);
            }

            return $next($request);
        }

        // 3. MAIN APPLICATION DOMAIN (e.g. carrental.local)
        // Admin portal routes should only be accessed on the admin portal subdomain
        if ($request->is('admin', 'admin/*')) {
            $portSuffix = ($request->getPort() && ! in_array($request->getPort(), [80, 443])) ? ':' . $request->getPort() : '';
            $portalHost = 'portal.' . $host;
            $portalBaseUrl = $request->getScheme() . '://' . $portalHost . $portSuffix;
            $targetUrl = $portalBaseUrl . '/' . ltrim($request->getRequestUri(), '/');

            if ($request->header('X-Inertia')) {
                return Inertia::location($targetUrl);
            }

            return redirect()->to($targetUrl);
        }

        // CRM routes should be accessed on the dedicated CRM portal subdomain
        if ($request->is('crm', 'crm/*')) {
            $portSuffix = ($request->getPort() && ! in_array($request->getPort(), [80, 443])) ? ':' . $request->getPort() : '';
            $crmHost = 'portal.crm' . ltrim($host, 'portal.');
            if (! str_contains($crmHost, 'crmcarrental.')) {
                $crmHost = 'portal.crmcarrental.local';
            }
            $crmBaseUrl = $request->getScheme() . '://' . $crmHost . $portSuffix;
            $targetUrl = $crmBaseUrl . '/' . ltrim($request->getRequestUri(), '/');

            if ($request->header('X-Inertia')) {
                return Inertia::location($targetUrl);
            }

            return redirect()->to($targetUrl);
        }

        return $next($request);
    }
}
