<?php

namespace App\Mail\User\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $passwordReset;

    public $link;

    public $user;

    public function __construct($passwordReset, $link, $user)
    {
        $this->passwordReset = $passwordReset;
        $this->link = $link;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = getEmailTemplate('employee', 'password_reset_email');
        $acceptedData = [
            'first_name' => ! empty($this->user) ? $this->user->first_name : null,
            'href' => ! empty($this->link) ? $this->link : null,
            'link_text' => 'Reset your password',
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
