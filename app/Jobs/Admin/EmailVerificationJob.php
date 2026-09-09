<?php

namespace App\Jobs\Admin;

use App\Mail\Admin\WelcomeEmailMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailVerificationJob implements ShouldQueue
{
    use Dispatchable ,InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $verification_code;

    public function __construct($user, $verification_code)
    {
        $this->user = $user;
        $this->verification_code = $verification_code;
    }

    public function handle(): void
    {
        setSMTP();
        $emailTemplate = getEmailTemplate('admin', 'verification_email');
        $acceptedData = [
            'first_name' => $this->user?->first_name,
            'verification_code' => $this->verification_code,
        ];

        $acceptedTag = [];
        $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);

        $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
        $content = renderEmailData($content, $acceptedInputs, $acceptedData);
        Mail::to($this->user->email)->send(new WelcomeEmailMail($content, $emailTemplate));
    }
}
