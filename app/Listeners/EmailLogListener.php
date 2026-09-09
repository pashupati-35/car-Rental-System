<?php

namespace App\Listeners;

use App\Services\EmailLog\EmailLogService;
use Illuminate\Mail\Events\MessageSent;

class EmailLogListener
{
    public function __construct(
        protected EmailLogService $emailLogService,
    ) {}

    /**
     * Handle the event when an email is sent.
     */
    public function handle(MessageSent $event): void
    {
        $this->emailLogService->logMessageSent($event);
    }
}
