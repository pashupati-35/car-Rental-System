<?php

namespace App\Jobs\Admin;

use App\Mail\Admin\WelcomeEmailMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CareerApplicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $careerApplication;

    public function __construct($careerApplication)
    {
        $this->careerApplication = $careerApplication;
    }

    public function handle(): void
    {
        $adminEmail = getAdminEmails();
        setSMTP();
        $emailTemplate = getEmailTemplate('admin', 'career_application_email');
        $acceptedData = [
            'first_name' => $this->careerApplication->first_name ?? null,
            'last_name' => $this->careerApplication->last_name ?? null,
            'email' => $this->careerApplication->email ?? null,
            'phone' => $this->careerApplication->phone ?? null,
            'career_title' => $this->careerApplication->career->title ?? null,
            'submitted_at' => $this->careerApplication->created_at ?? null,
        ];

        $acceptedTag = [];
        $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);

        $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
        $content = renderEmailData($content, $acceptedInputs, $acceptedData);

        Mail::to($adminEmail)->send(new WelcomeEmailMail($content, $emailTemplate));
    }

    public function attachments(): array
    {
        $attachments = [];
        if ($this->careerApplication->file_path) {
            $attachments[] = [
                'path' => $this->careerApplication->file_path,
                'name' => $this->careerApplication->file,
            ];
        }

        return $attachments;
    }
}
