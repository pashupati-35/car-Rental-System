<?php

namespace App\Jobs\Admin;

use App\Mail\Admin\WelcomeEmailMail;
use App\Models\Customer;
use App\Models\Owner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function handle(): void
    {
        try {
            setSMTP();
        } catch (\Throwable $e) {
            //
        }

        $role = 'admin';
        if ($this->user instanceof Owner) {
            $role = 'owner';
        } elseif ($this->user instanceof Customer) {
            $role = 'customer';
        }

        $emailTemplate = getEmailTemplate($role, 'welcome_email') ?? getEmailTemplate('admin', 'welcome_email');
        if (! $emailTemplate) {
            return;
        }

        $acceptedData = [
            'first_name' => $this->user->first_name ?? $this->user->name ?? $this->user->full_name,
            'email' => $this->user->email ?? null,
            'password' => $this->password ?? null,
        ];

        $acceptedTag = [];
        $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);

        $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
        $content = renderEmailData($content, $acceptedInputs, $acceptedData);

        Mail::to($this->user->email)->send(new WelcomeEmailMail($content, $emailTemplate));
    }
}
