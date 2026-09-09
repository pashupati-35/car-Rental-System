<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginListener
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Login $event)
    {
        if (
            ! config('user-activity.log_events.on_login', false)
            || ! config('user-activity.activated', true)
        ) {
            return;
        }

        $user = $event->user;
        $userId = $adminUserId = null;

        if ($event->guard == 'web') {
            $userId = $user->id;
            $title = 'User login successful.';
        }

        if ($event->guard == 'admin') {
            $adminUserId = $user->id;
            $title = 'Admin login successful.';
        }

        if ($event->guard == 'employee') {
            $adminUserId = $user->id;
            $title = 'Employee login successful.';
        }

        $dateTime = date('Y-m-d H:i:s');

        $data = [
            'ip' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ];

        DB::table('logs')->insert([
            'title' => $title,
            'user_id' => $userId,
            'admin_user_id' => $adminUserId,
            'log_date' => $dateTime,
            'table_name' => '',
            'log_type' => 'login',
            'data' => json_encode($data),
        ]);
    }
}
