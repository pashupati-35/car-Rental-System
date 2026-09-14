<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class EnquiryDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $email = null,
        public ?string $subject = null,
        public ?string $message = null,
        public ?string $phone = null,
        public ?string $token = null,
        public ?bool $mark_as_read = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            slug: $data['slug'] ?? null,
            email: $data['email'] ?? null,
            subject: $data['subject'] ?? null,
            message: $data['message'] ?? null,
            phone: $data['phone'] ?? null,
            token: $data['token'] ?? null,
            mark_as_read: isset($data['mark_as_read']) ? (bool) $data['mark_as_read'] : false,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            slug: $model->slug,
            email: $model->email,
            subject: $model->subject,
            message: $model->message,
            phone: $model->phone,
            token: $model->token,
            mark_as_read: (bool) $model->mark_as_read,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'phone' => $this->phone,
            'token' => $this->token,
            'mark_as_read' => $this->mark_as_read,
        ], fn ($value) => $value !== null);
    }
}
