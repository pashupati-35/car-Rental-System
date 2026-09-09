<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class ApprovePayrollItemRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;

    public $emailTemplate;

    protected $attachmentPath;

    protected $attachmentName;

    public function __construct($content, $emailTemplate, $attachmentPath, $attachmentName)
    {
        $this->content = $content;
        $this->emailTemplate = $emailTemplate;
        $this->attachmentPath = $attachmentPath;
        $this->attachmentName = $attachmentName;
    }

    public function build()
    {
        return $this->view('emails.email')
            ->subject($this->emailTemplate->subject)
            ->from(env('MAIL_FROM_ADDRESS'));
    }

    public function attachments()
    {
        if (! empty($this->attachmentPath) && ! empty($this->attachmentName)) {
            return [
                Attachment::fromPath($this->attachmentPath)
                    ->as($this->attachmentName)
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
