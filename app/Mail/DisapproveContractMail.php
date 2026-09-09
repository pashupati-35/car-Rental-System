<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisapproveContractMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;

    public $emailTemplate;

    public function __construct($content, $emailTemplate)
    {
        $this->content = $content;
        $this->emailTemplate = $emailTemplate;
    }

    public function build()
    {
        return $this->view('emails.email')
            ->subject($this->emailTemplate->subject)
            ->from(env('MAIL_FROM_ADDRESS'));
    }
}
