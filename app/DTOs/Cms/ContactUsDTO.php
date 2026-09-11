<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class ContactUsDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $type = null,
        public ?string $first_name = null,
        public ?string $last_name = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $subject = null,
        public ?string $message = null,
        public ?bool $is_read = false,
        public ?bool $replied = false,
        public ?bool $is_active = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            type: $data['type'] ?? null,
            first_name: $data['first_name'] ?? null,
            last_name: $data['last_name'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            subject: $data['subject'] ?? null,
            message: $data['message'] ?? null,
            is_read: isset($data['is_read']) ? (bool) $data['is_read'] : false,
            replied: isset($data['replied']) ? (bool) $data['replied'] : false,
            is_active: isset($data['is_active']) ? (bool) $data['is_active'] : true,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            type: $model->type,
            first_name: $model->first_name,
            last_name: $model->last_name,
            phone: $model->phone,
            email: $model->email,
            subject: $model->subject,
            message: $model->message,
            is_read: (bool) $model->is_read,
            replied: (bool) $model->replied,
            is_active: (bool) $model->is_active,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'type' => $this->type,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'is_read' => $this->is_read,
            'replied' => $this->replied,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
