<?php

namespace App\Mail\Admin\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $verification_code;

    public $emailTemplate;

    public $content;

    public function __construct($user, $verification_code)
    {
        $this->user = $user;
        $this->verification_code = $verification_code;
        $this->emailTemplate = getEmailTemplate('admin', 'verification_email');
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->emailTemplate->title,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content()
    {
        if (empty($this->emailTemplate)) {
            return new Content(
                view: 'emails.email',
            );
        }
        $acceptedData = [
            'first_name' => ! empty($this->user) ? $this->user->first_name : null,
            'verification_code' => ! empty($this->verification_code) ? $this->verification_code : null,

        ];
        /* Tags that needs to be rendered */
        $acceptedTag = [];
        /* Tags that needs to be rendered */

        /* Append Tag Data to HTML */
        $acceptedInputs = normalizeEmailTemplateInputs($this->emailTemplate->accepted_inputs);
        /* array_push($acceptedInputs,'href'); */
        /* array_push($acceptedInputs,'link_text'); */
        /* Append Tag Data to HTML */

        $content = renderEmailHTML($this->emailTemplate->description, $acceptedTag);
        $this->content = renderEmailData($content, $acceptedInputs, $acceptedData);

        return new Content(
            view: 'emails.email',
        );

    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
