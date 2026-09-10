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
        try {
            setSMTP();
        } catch (\Throwable $e) {
            // Ignore SMTP set failure if local
        }

        $emailTemplate = getEmailTemplate('admin', 'verification_email') ?: getEmailTemplate('admin', 'welcome_email');
        if ($emailTemplate) {
            $acceptedData = [
                'first_name' => $this->user?->first_name ?: $this->user?->name,
                'name' => $this->user?->name,
                'verification_code' => $this->verification_code,
            ];

            $acceptedTag = [];
            $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);

            $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
            $content = renderEmailData($content, $acceptedInputs, $acceptedData);
            Mail::to($this->user->email)->send(new WelcomeEmailMail($content, $emailTemplate));
        } else {
            Mail::raw("Your AutoRent administrative login verification code is: {$this->verification_code}. This code expires in 10 minutes.", function ($message) {
                $message->to($this->user->email)
                    ->subject('AutoRent Admin - Login Verification Code');
            });
        }
    }
}
