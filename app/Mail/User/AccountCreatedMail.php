<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;

    private $emailTemplate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content, $emailTemplate)
    {
        $this->content = $content;
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.email')
            ->subject($this->emailTemplate->subject)
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
    }
}
