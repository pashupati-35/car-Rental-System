<?php

namespace App\Jobs\Admin;

use App\Mail\Admin\WelcomeEmailMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;

    protected $password;

    public function __construct($employee, $password)
    {
        $this->employee = $employee;
        $this->password = $password;
    }

    public function handle(): void
    {
        setSMTP();
        $emailTemplate = getEmailTemplate('admin', 'welcome_email');
        $acceptedData = [
            'first_name' => $this->employee->first_name ?? null,
            'email' => $this->employee->email ?? null,
            'password' => $this->password ?? null,
        ];

        $acceptedTag = [];
        $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);

        $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
        $content = renderEmailData($content, $acceptedInputs, $acceptedData);

        Mail::to($this->employee->email)->send(new WelcomeEmailMail($content, $emailTemplate));
    }
}
