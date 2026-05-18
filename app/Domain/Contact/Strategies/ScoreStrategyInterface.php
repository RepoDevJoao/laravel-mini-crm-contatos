<?php

namespace App\Domain\Contact\Strategies;

use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;

interface ScoreStrategyInterface
{
    public function calculate(Email $email, string $name, string $phone): int;
}
