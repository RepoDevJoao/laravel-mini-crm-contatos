<?php

namespace App\Domain\Contact\ValueObjects;

use InvalidArgumentException;

class Phone
{
    private const SP_DDDS = [11, 12, 13, 14, 15, 16, 17, 18, 19];

    private const VALID_DDDS = [
        11, 12, 13, 14, 15, 16, 17, 18, 19,
        21, 22, 24,
        27, 28,
        31, 32, 33, 34, 35, 37, 38,
        41, 42, 43, 44, 45, 46,
        47, 48, 49,
        51, 53, 54, 55,
        61, 62, 63, 64, 65, 66, 67, 68, 69,
        71, 73, 74, 75, 77, 79,
        81, 82, 83, 84, 85, 86, 87, 88, 89,
        91, 92, 93, 94, 95, 96, 97, 98, 99,
    ];

    private string $phone;

    public function __construct(string $phone)
    {
        $normalized = preg_replace('/\D/', '', $phone);

        if (strlen($normalized) < 10 || strlen($normalized) > 11) {
            throw new InvalidArgumentException("Invalid phone: {$phone}");
        }

        $this->phone = $normalized;
    }

    public function value(): string
    {
        return $this->phone;
    }

    public function getDdd(): int
    {
        return (int) substr($this->phone, 0, 2);
    }

    public function isSaoPauloDdd(): bool
    {
        return in_array($this->getDdd(), self::SP_DDDS, true);
    }

    public function hasValidDdd(): bool
    {
        return in_array($this->getDdd(), self::VALID_DDDS, true);
    }
}
