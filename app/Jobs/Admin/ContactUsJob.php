<?php

namespace App\Jobs\Admin;

use App\Mail\Admin\WelcomeEmailMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ContactUsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contact;

    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    public function handle(): void
    {
        $adminEmail = getAdminEmails();
        setSMTP();
        $emailTemplate = getEmailTemplate('admin', 'contact_us_email');
        $acceptedData = [
            'first_name' => $this->contact->first_name ?? null,
            'last_name' => $this->contact->last_name ?? null,
            'email' => $this->contact->email ?? null,
            'phone' => $this->contact->phone ?? null,
            'subject' => $this->contact->subject ?? null,
            'message' => $this->contact->message ?? null,
        ];

        $acceptedTag = [];
        $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);

        $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
        $content = renderEmailData($content, $acceptedInputs, $acceptedData);

        Mail::to($adminEmail)->send(new WelcomeEmailMail($content, $emailTemplate));
    }
}
