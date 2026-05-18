<?php

namespace App\Domain\Contact\Strategies;

use App\Domain\Contact\ValueObjects\Email;

class NameScoreStrategy implements ScoreStrategyInterface
{
    public function calculate(Email $email, string $name, string $phone): int
    {
        $words = array_filter(explode(' ', trim($name)));

        return count($words) > 1 ? 10 : 0;
    }
}