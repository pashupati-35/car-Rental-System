<?php

namespace App\Listeners;

use App\Models\ActivityLog\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LoginListener
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Login $event)
    {
        try {
            if (
                ! config('user-activity.log_events.on_login', true)
                || ! config('user-activity.activated', true)
            ) {
                return;
            }

            $user = $event->user;
            $guard = $event->guard ?? 'web';
            $title = ucfirst($guard) . ' login successful.';

            if (Schema::hasTable('activity_logs')) {
                ActivityLog::create([
                    'log_type' => 'login',
                    'description' => $title,
                    'causer_type' => $user ? get_class($user) : null,
                    'causer_id' => $user ? $user->id : null,
                    'ip_address' => $this->request->ip(),
                    'user_agent' => $this->request->userAgent(),
                    'table_name' => $user ? $user->getTable() : '',
                    'properties' => [
                        'guard' => $guard,
                        'email' => $user->email ?? null,
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            // Log failure should never block or abort authentication
            report($e);
        }
    }
}

