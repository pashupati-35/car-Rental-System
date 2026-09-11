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
        $isPortal = str_starts_with($host, 'portal.');
        $mainHost = $isPortal ? preg_replace('/^portal\./', '', $host) : $host;
        $portalHost = $isPortal ? $host : 'portal.'.$host;
        $adminPortalBaseUrl = $scheme.'://'.$portalHost.$portSuffix;
        $mainAppBaseUrl = config('app.url') ? rtrim(config('app.url'), '/') : ($scheme.'://'.$mainHost.$portSuffix);

        return array_merge(parent::share($request), [
            'isPortal' => $isPortal,
            'adminPortalUrl' => $adminPortalBaseUrl,
            'mainAppUrl' => $mainAppBaseUrl,
            'auth' => [
                'admin' => fn () => $request->user('admin') ? (new \App\Http\Resources\AdminResource($request->user('admin')))->resolve() : null,
                'owner' => fn () => $request->user('owner') ? (new \App\Http\Resources\OwnerResource($request->user('owner')))->resolve() : null,
                'customer' => fn () => $request->user('customer') ? (new \App\Http\Resources\CustomerResource($request->user('customer')))->resolve() : null,
                'user' => fn () => $request->user('admin')
                    ? (new \App\Http\Resources\AdminResource($request->user('admin')))->resolve()
                    : ($request->user('owner')
                        ? (new \App\Http\Resources\OwnerResource($request->user('owner')))->resolve()
                        : ($request->user('customer')
                            ? (new \App\Http\Resources\CustomerResource($request->user('customer')))->resolve()
                            : null)),
                'isImpersonating' => fn () => (bool) ($request->session()->get('admin_impersonating') || ($request->user('admin') && ($request->user('owner') || $request->user('customer')))),
                'adminPortalUrl' => $adminPortalBaseUrl,
                'mainAppUrl' => $mainAppBaseUrl,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'message' => fn () => $request->session()->get('message'),
            ],
            'appName' => fn () => getSiteSetting()?->company_name ?: config('app.name', 'Car Rental System'),
            'siteSettings' => fn () => getSiteSettingLogos(),
            'adminCounts' => fn () => $request->user('admin') ? AdminCountCacheService::getSharedCounts() : null,
        ]);
    }
}
