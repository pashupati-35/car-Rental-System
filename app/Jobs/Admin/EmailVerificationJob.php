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

        $role = 'admin';
        if ($this->user instanceof \App\Models\Owner) {
            $role = 'owner';
        } elseif ($this->user instanceof \App\Models\Customer) {
            $role = 'customer';
        }

        $emailTemplate = getEmailTemplate($role, 'email_verification_code')
            ?: getEmailTemplate($role, 'verification_code_email')
            ?: getEmailTemplate($role, 'mfa_verification_email')
            ?: getEmailTemplate($role, 'verification_email');

        if ($emailTemplate) {
            $acceptedData = [
                'first_name' => $this->user?->first_name ?: $this->user?->name ?: $this->user?->full_name ?: 'User',
                'name' => $this->user?->name ?: $this->user?->full_name ?: $this->user?->first_name ?: 'User',
                'email' => $this->user?->email,
                'verification_code' => (string) $this->verification_code,
                'code' => (string) $this->verification_code,
            ];

            $acceptedTag = [];
            $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);

            $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
            $content = renderEmailData($content, $acceptedInputs, $acceptedData);
            Mail::to($this->user->email)->send(new WelcomeEmailMail($content, $emailTemplate));
        } else {
            $portalName = ucfirst($role);
            Mail::raw("Your AutoRent {$portalName} login verification code is: {$this->verification_code}. This code expires in 10 minutes.", function ($message) use ($portalName) {
                $message->to($this->user->email)
                    ->subject("AutoRent {$portalName} - Login Verification Code");
            });
        }
    }
}
