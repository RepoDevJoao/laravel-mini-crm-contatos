<?php

namespace App\Domain\Contact\Services;

use App\Domain\Contact\Strategies\ScoreStrategyInterface;
use App\Domain\Contact\ValueObjects\Email;

class ContactScoreCalculator
{
    /**
     * @param ScoreStrategyInterface[] $strategies
     */
    public function __construct(
        private readonly array $strategies
    ) {}

    public function calculate(Email $email, string $name, string $phone): int
    {
        $score = 0;

        foreach ($this->strategies as $strategy) {
            $score += $strategy->calculate($email, $name, $phone);
        }

        return $score;
    }
}