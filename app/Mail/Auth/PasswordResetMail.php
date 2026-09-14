<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $resetUrl,
        public string $role = 'Customer'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reset Your Password - {$this->role} Account"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password_reset',
        );
    }
}
