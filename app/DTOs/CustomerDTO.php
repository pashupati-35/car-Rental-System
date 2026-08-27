<?php

namespace App\DTOs;

use Illuminate\Database\Eloquent\Model;

readonly class CustomerDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone_number = null,
        public ?string $address = null,
        public ?string $gender = null,
        public ?string $password = null,
        public ?int $admin_id = null,
        public ?int $owner_id = null,
        public ?\DateTime $email_verified_at = null,
        public ?\DateTime $created_at = null,
        public ?\DateTime $updated_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            phone_number: $data['phone_number'] ?? null,
            address: $data['address'] ?? null,
            gender: $data['gender'] ?? null,
            password: $data['password'] ?? null,
            admin_id: $data['admin_id'] ?? null,
            owner_id: $data['owner_id'] ?? null,
            email_verified_at: isset($data['email_verified_at']) ? new \DateTime($data['email_verified_at']) : null,
            created_at: isset($data['created_at']) ? new \DateTime($data['created_at']) : null,
            updated_at: isset($data['updated_at']) ? new \DateTime($data['updated_at']) : null,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            email: $model->email,
            phone_number: $model->phone_number,
            address: $model->address,
            gender: $model->gender,
            password: $model->password,
            admin_id: $model->admin_id,
            owner_id: $model->owner_id,
            email_verified_at: $model->email_verified_at,
            created_at: $model->created_at,
            updated_at: $model->updated_at,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'gender' => $this->gender,
            'password' => $this->password,
            'admin_id' => $this->admin_id,
            'owner_id' => $this->owner_id,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ], fn ($value) => $value !== null);
    }
}
