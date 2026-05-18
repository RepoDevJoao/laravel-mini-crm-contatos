<?php

namespace App\Domain\Contact\ValueObjects;

use InvalidArgumentException;

class Email
{
    private const NON_CORPORATE_DOMAINS = [
        'gmail.com',
        'hotmail.com',
        'yahoo.com',
        'outlook.com',
        'live.com',
        'icloud.com',
    ];

    private string $email;

    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email: {$email}");
        }

        $this->email = strtolower($email);
    }

    public function value(): string
    {
        return $this->email;
    }

    public function isCorporate(): bool
    {
        $domain = $this->getDomain();

        return !in_array($domain, self::NON_CORPORATE_DOMAINS, true);
    }

    public function isFromBrazil(): bool
    {
        return str_ends_with($this->email, '.br');
    }

    private function getDomain(): string
    {
        return substr($this->email, strpos($this->email, '@') + 1);
    }
}
