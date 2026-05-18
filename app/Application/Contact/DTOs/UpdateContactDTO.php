<?php

namespace App\Application\Contact\DTOs;

class UpdateContactDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:  $data['name'],
            email: $data['email'],
            phone: $data['phone'],
        );
    }
}