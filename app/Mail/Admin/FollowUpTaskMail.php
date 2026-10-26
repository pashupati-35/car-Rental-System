<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FollowUpTaskMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $content;

    public $emailTemplate;

    public ?string $customSubject;

    public function __construct(string $content, $emailTemplate, ?string $customSubject = null)
    {
        $this->content = $content;
        $this->emailTemplate = $emailTemplate;
        $this->customSubject = $customSubject;
    }

    public function build()
    {
        $subject = $this->customSubject ?: ($this->emailTemplate?->subject ?? 'Follow-Up Notification');
        $fromAddress = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
        $fromName = config('mail.from.name') ?: config('app.name', 'AutoRent');

        $mail = $this->view('emails.email')
            ->subject($subject);

        if ($fromAddress) {
            $mail->from($fromAddress, $fromName);
        }

        return $mail;
    }
}
