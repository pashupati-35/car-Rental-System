<?php

namespace App\Services\Traits;

use App\Models\EmailTemplate\EmailTemplate;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\Blade;

trait MailTemplate
{
    public function getTemplate(string $role, string $type)
    {
        return EmailTemplate::where([
            'role' => $role,
            'type' => $type,
        ])->first();
    }

    public function sanitize($data, $template, $isMessageContent = false): string
    {
        $content = $isMessageContent ? $this->prepareContent($data, $template->message_content) : $this->prepareContent($data, $template->description);

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', storage_path('app/purifier'));

        // Allow these tags and attributes
        $config->set('HTML.Allowed', 'p,br,strong,em,a[href|target|rel]');

        // Allow target="_blank"
        $config->set('HTML.TargetBlank', true);

        // Allow _blank as a valid target value
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        // Optional: automatically add rel="noopener" for security
        $config->set('Attr.DefaultImageAlt', '');

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($content);
    }

    private function prepareContent($data, $description)
    {
        return Blade::render($description, data: $data);
    }
}
