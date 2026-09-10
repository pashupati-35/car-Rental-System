<?php

namespace App\DTOs\Filters;

readonly class SiteSettingFilterDTO
{
    public function __construct(
        public ?string $search = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
        );
    }
}
