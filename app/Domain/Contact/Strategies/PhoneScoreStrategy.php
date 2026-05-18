<?php

namespace App\Domain\Contact\Strategies;

use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;

class PhoneScoreStrategy implements ScoreStrategyInterface
{
    public function calculate(Email $email, string $name, string $phone): int
    {
        if (empty($phone)) {
            return 0;
        }

        try {
            $phoneVO = new Phone($phone);
        } catch (\InvalidArgumentException) {
            return 0;
        }

        if ($phoneVO->isSaoPauloDdd()) {
            return 20;
        }

        if ($phoneVO->hasValidDdd()) {
            return 10;
        }

        return 0;
    }
}