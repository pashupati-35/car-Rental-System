<?php

namespace App\Services\EmailLog;

use App\Models\EmailLog\EmailLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class EmailLogService
{
    /**
     * Log a sent email from a MessageSent event.
     */
    public function logMessageSent(MessageSent $event): ?EmailLog
    {
        try {
            $message = $event->message;

            $from = $this->formatAddresses($message instanceof Email ? $message->getFrom() : []);
            $to = $this->formatAddresses($message instanceof Email ? $message->getTo() : []);
            $cc = $this->formatAddresses($message instanceof Email ? $message->getCc() : []);
            $bcc = $this->formatAddresses($message instanceof Email ? $message->getBcc() : []);
            $replyTo = $this->formatAddresses($message instanceof Email ? $message->getReplyTo() : []);

            $subject = $message instanceof Email ? (string) $message->getSubject() : null;
            $body = $this->extractBody($message);
            $attachments = $this->extractAttachments($message);

            $mailableClass = null;
            if (isset($event->data['__laravel_mailable'])) {
                $mailableClass = is_object($event->data['__laravel_mailable'])
                    ? get_class($event->data['__laravel_mailable'])
                    : (string) $event->data['__laravel_mailable'];
            }

            $sender = $this->resolveSender();

            return EmailLog::query()->create([
                'sender_type' => $sender ? $sender->getMorphClass() : null,
                'sender_id' => $sender?->getKey(),
                'from' => $from ?: config('mail.from.address'),
                'to' => $to ?: 'N/A',
                'cc' => $cc ?: null,
                'bcc' => $bcc ?: null,
                'reply_to' => $replyTo ?: null,
                'subject' => $subject,
                'body' => $body,
                'status' => 'sent',
                'mailable_class' => $mailableClass,
                'transport' => config('mail.default'),
                'ip_address' => Request::ip(),
                'user_agent' => mb_substr((string) Request::userAgent(), 0, 65535),
                'attachments' => $attachments ?: null,
                'headers' => !empty($event->data) ? ['data_keys' => array_keys($event->data)] : null,
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /**
     * Explicitly log an email entry.
     */
    public function log(
        string $to,
        string $subject,
        ?string $body = null,
        string $status = 'sent',
        ?string $from = null,
        ?string $cc = null,
        ?string $bcc = null,
        ?string $errorMessage = null,
        ?Model $sender = null,
        ?array $attachments = null,
    ): EmailLog {
        $sender ??= $this->resolveSender();

        return EmailLog::query()->create([
            'sender_type' => $sender ? $sender->getMorphClass() : null,
            'sender_id' => $sender?->getKey(),
            'from' => $from ?: config('mail.from.address'),
            'to' => $to,
            'cc' => $cc,
            'bcc' => $bcc,
            'subject' => $subject,
            'body' => $body,
            'status' => $status,
            'transport' => config('mail.default'),
            'error_message' => $errorMessage,
            'ip_address' => Request::ip(),
            'user_agent' => mb_substr((string) Request::userAgent(), 0, 65535),
            'attachments' => $attachments,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }

    /**
     * Format an array of addresses into a readable string.
     *
     * @param  Address[]|array  $addresses
     */
    private function formatAddresses(array $addresses): string
    {
        return collect($addresses)
            ->map(function ($address) {
                if ($address instanceof Address) {
                    return $address->getName()
                        ? "{$address->getName()} <{$address->getAddress()}>"
                        : $address->getAddress();
                }

                return (string) $address;
            })
            ->filter()
            ->implode(', ');
    }

    /**
     * Extract HTML or plain text body from the message.
     */
    private function extractBody(mixed $message): ?string
    {
        if ($message instanceof Email) {
            $html = $message->getHtmlBody();
            if ($html !== null) {
                return is_resource($html) ? stream_get_contents($html) : (string) $html;
            }

            $text = $message->getTextBody();
            if ($text !== null) {
                return is_resource($text) ? stream_get_contents($text) : (string) $text;
            }

            $body = $message->getBody();
            if ($body !== null) {
                return $body->bodyToString();
            }
        } elseif (is_object($message) && method_exists($message, 'toString')) {
            return $message->toString();
        }

        return null;
    }

    /**
     * Extract attachment information from the message.
     */
    private function extractAttachments(mixed $message): array
    {
        if (! $message instanceof Email) {
            return [];
        }

        $attachments = [];
        foreach ($message->getAttachments() as $part) {
            if ($part instanceof DataPart) {
                $attachments[] = [
                    'name' => $part->getFilename() ?: 'attachment',
                    'media_type' => $part->getMediaType().'/'.$part->getMediaSubtype(),
                ];
            }
        }

        return $attachments;
    }

    /**
     * Resolve the currently authenticated user across admin/owner/customer guards.
     */
    private function resolveSender(): ?Model
    {
        $guards = array_keys(config('auth.guards', []));
        if (empty($guards)) {
            $guards = ['admin', 'owner', 'customer'];
        }

        foreach ($guards as $guard) {
            try {
                if (Auth::guard($guard)->check()) {
                    $user = Auth::guard($guard)->user();
                    if ($user instanceof Model) {
                        return $user;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore guard resolve failure
            }
        }

        return null;
    }
}
