<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            // Redirect based on the URL path
            if ($request->is('admin/*')) {
                return redirect()->route('admin.login')->with('error', 'Please login to access the admin area.');
            }

            if ($request->is('owner/*')) {
                return redirect()->route('owner.login')->with('error', 'Please login to access the owner area.');
            }

            if ($request->is('customer/*')) {
                return redirect()->route('customer.login')->with('error', 'Please login to access your dashboard.');
            }

            // Optionally handle default redirects for other users
            return redirect()->route('customer.login')->with('error', 'Unauthorized access.');
        }

        return parent::render($request, $exception);
    }
}
