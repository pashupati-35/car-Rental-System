<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\EnsureDomainAccess;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\OwnerMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'owner' => OwnerMiddleware::class,
            'customer' => CustomerMiddleware::class,
            'guest' => RedirectIfAuthenticated::class,
        ]);
        $middleware->redirectTo(
            guests: function (Request $request) {
                if ($request->is('admin', 'admin/*') || str_starts_with($request->getHost(), 'portal.')) {
                    return route('admin.login');
                }
                if ($request->is('owner', 'owner/*')) {
                    return route('owner.login');
                }

                return route('customer.login');
            }
        );
        $middleware->web(append: [
            EnsureDomainAccess::class,
            HandleInertiaRequests::class,
        ]);
        $middleware->api(prepend: [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            abort(404);
        });
    })->create();
