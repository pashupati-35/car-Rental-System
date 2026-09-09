<?php

namespace App\Mail\User\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserAccountRecoveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $userVerification;

    public $verificationUrl;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $userVerification, $verificationUrl)
    {
        $this->user = $user;
        $this->userVerification = $userVerification;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = getEmailTemplate('user', 'recovery_email');

        $acceptedData = [
            'email' => ! empty($this->user) ? $this->user->email : null,
            'first_name' => ! empty($this->user) ? $this->user->first_name : null,
            'verification_code' => ! empty($this->userVerification) ? $this->userVerification->verification_code : null,
            'href' => ! empty($this->verificationUrl) ? $this->verificationUrl : null,
            'link_text' => 'Recover Account',
        ];

        /* Tags that needs to be rendered */
        $acceptedTag = ['link'];
        /* Tags that needs to be rendered */

        /* Append Tag Data to HTML */
        $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);
        array_push($acceptedInputs, 'href');
        array_push($acceptedInputs, 'link_text');
        /* Append Tag Data to HTML */

        $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
        $content = renderEmailData($content, $acceptedInputs, $acceptedData);

        return $this->view('emails.email', compact('content'))
            ->subject($emailTemplate->title)
            ->from(env('MAIL_FROM_ADDRESS'));
    }
}
