<?php

namespace App\Mail\User\Auth;

use App\Models\Admin;
use App\Models\Owner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $userVerification;

    public $verificationUrl;

    public function __construct($user, $userVerification, $verificationUrl)
    {
        $this->user = $user;
        $this->userVerification = $userVerification;
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        $role = 'customer';
        if ($this->user instanceof Admin) {
            $role = 'admin';
        } elseif ($this->user instanceof Owner) {
            $role = 'owner';
        }

        $emailTemplate = getEmailTemplate($role, 'verification_email')
            ?? getEmailTemplate('admin', 'verification_email');

        $verificationCode = $this->userVerification?->verification_code;

        $acceptedData = [
            'email' => $this->user?->email,
            'first_name' => $this->user?->first_name,
            'verification_code' => $verificationCode,
            'href' => $this->verificationUrl,
            'link_text' => 'Confirm Email',
        ];

        if (empty($emailTemplate)) {
            $content = '<p>Hello '.e($acceptedData['first_name']).',</p>'
                .'<p>Your verification code is: <strong>'.e($verificationCode).'</strong></p>'
                .'<p>Or <a href="'.e($acceptedData['href']).'">click here</a> to verify your email.</p>';

            return $this->view('emails.email', compact('content'))
                ->subject('Email Verification')
                ->from(env('MAIL_FROM_ADDRESS'));
        }

        $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);
        $acceptedInputs = array_values(array_unique(array_merge(
            $acceptedInputs,
            ['verification_code', 'href', 'link_text']
        )));

        $content = renderEmailHTML($emailTemplate->description, []);
        $content = renderEmailData($content, $acceptedInputs, $acceptedData);

        return $this->view('emails.email', compact('content'))
            ->subject($emailTemplate->title)
            ->from(env('MAIL_FROM_ADDRESS'));
    }
}
