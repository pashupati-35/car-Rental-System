<?php

namespace App\Http\Middleware;

use App\Services\Admin\AdminCountCacheService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    public function rootView(Request $request): string
    {
        return $this->rootView;
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $host = $request->getHost();
        $scheme = $request->getScheme();
        $port = $request->getPort();
        $portSuffix = ($port && ! in_array($port, [80, 443])) ? ':'.$port : '';
        $portalHost = str_starts_with($host, 'portal.') ? $host : 'portal.'.$host;
        $adminPortalBaseUrl = $scheme.'://'.$portalHost.$portSuffix;

        return array_merge(parent::share($request), [
            'auth' => [
                'admin' => fn () => $request->user('admin'),
                'owner' => fn () => $request->user('owner'),
                'customer' => fn () => $request->user('customer'),
                'user' => fn () => $request->user('admin') ?? $request->user('owner') ?? $request->user('customer'),
                'isImpersonating' => fn () => (bool) ($request->session()->get('admin_impersonating') || ($request->user('admin') && ($request->user('owner') || $request->user('customer')))),
                'adminPortalUrl' => $adminPortalBaseUrl,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'message' => fn () => $request->session()->get('message'),
            ],
            'appName' => config('app.name', 'Car Rental System'),
            'adminCounts' => fn () => $request->user('admin') ? AdminCountCacheService::getSharedCounts() : null,
        ]);
    }
}
