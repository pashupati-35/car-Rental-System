<?php

namespace App\Models\EmailLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmailLog extends Model
{
    protected $table = 'email_logs';

    protected $fillable = [
        'sender_type',
        'sender_id',
        'from',
        'to',
        'cc',
        'bcc',
        'reply_to',
        'subject',
        'body',
        'status',
        'mailable_class',
        'transport',
        'error_message',
        'ip_address',
        'user_agent',
        'attachments',
        'headers',
        'sent_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'headers' => 'array',
        'sent_at' => 'datetime',
    ];

    protected $appends = [
        'sender_name',
        'sender_role',
    ];

    public function getSenderNameAttribute(): ?string
    {
        if ($this->sender) {
            return $this->sender->full_name ?? $this->sender->name ?? $this->sender->email ?? 'User #'.$this->sender_id;
        }

        return 'System Automated';
    }

    public function getSenderRoleAttribute(): string
    {
        if (! $this->sender_type) {
            return 'System';
        }
        $base = class_basename($this->sender_type);

        return match ($base) {
            'Admin', 'AdminUser' => 'Admin',
            'Owner' => 'Fleet Owner',
            'Customer', 'User' => 'Customer',
            default => $base,
        };
    }

    /**
     * Who triggered the email (AdminUser, Employee, User).
     */
    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to a specific status (sent, failed, etc.).
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to a specific recipient email.
     */
    public function scopeToEmail($query, string $email)
    {
        return $query->where('to', 'like', "%{$email}%");
    }

    /**
     * Scope to a specific sender/actor.
     */
    public function scopeBySender($query, Model $sender)
    {
        return $query->where('sender_type', $sender->getMorphClass())
            ->where('sender_id', $sender->getKey());
    }
}
