<?php

namespace App\Listeners;

use App\Models\ActivityLog\ActivityLog;
use App\Services\ActivityLog\ActivityLogService;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Auth\Events\Logout as LogoutEvent;
use Illuminate\Http\Request;

class ActivityLogAuthListener
{
    public function __construct(
        protected Request $request,
        protected ActivityLogService $activityLog,
    ) {}

    /**
     * Handle login events.
     */
    public function handleLogin(LoginEvent $event): void
    {
        $user = $event->user;
        if (! $user instanceof \Illuminate\Database\Eloquent\Model) {
            return;
        }

        $guard = $event->guard;
        if ($this->wasHandled('login', $user, $guard)) {
            return;
        }

        $title = match ($guard) {
            'admin' => 'Admin login successful.',
            'owner' => 'Owner login successful.',
            'customer' => 'Customer login successful.',
            default => 'User login successful.',
        };

        $this->activityLog->log(
            logType: 'login',
            description: $title,
            subject: $user,
            properties: [
                'ip' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'guard' => $guard,
            ],
            causer: $user,
            tableName: $user->getTable(),
        );
    }

    /**
     * Handle logout events.
     */
    public function handleLogout(LogoutEvent $event): void
    {
        $user = $event->user;
        if (! $user instanceof \Illuminate\Database\Eloquent\Model) {
            return;
        }

        $guard = property_exists($event, 'guard') ? $event->guard : 'customer';
        if ($this->wasHandled('logout', $user, $guard)) {
            return;
        }

        $title = match ($guard) {
            'admin' => 'Admin logout.',
            'owner' => 'Owner logout.',
            'customer' => 'Customer logout.',
            default => 'User logout.',
        };

        $this->activityLog->log(
            logType: 'logout',
            description: $title,
            subject: $user,
            properties: [
                'ip' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'guard' => $guard,
            ],
            causer: $user,
            tableName: $user->getTable(),
        );
    }

    private function wasHandled(string $action, object $user, ?string $guard): bool
    {
        $key = 'activity_log_auth.'.sha1($action.'|'.$guard.'|'.get_class($user).'|'.$user->getKey());

        if ($this->request->attributes->has($key)) {
            return true;
        }

        $this->request->attributes->set($key, true);

        return false;
    }
}