<?php

namespace App\Domain\Contact\Strategies;

use App\Domain\Contact\ValueObjects\Email;

class EmailScoreStrategy implements ScoreStrategyInterface
{
    public function calculate(Email $email, string $name, string $phone): int
    {
        $score = 0;

        if ($email->isCorporate()) {
            $score += 20;
        }

        if ($email->isFromBrazil()) {
            $score += 10;
        }

        return $score;
    }
}
