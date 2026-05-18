<?php

namespace Tests\Unit\Domain\Services;

use App\Domain\Contact\Services\ContactScoreCalculator;
use App\Domain\Contact\Strategies\EmailScoreStrategy;
use App\Domain\Contact\Strategies\NameScoreStrategy;
use App\Domain\Contact\Strategies\PhoneScoreStrategy;
use App\Domain\Contact\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class ContactScoreCalculatorTest extends TestCase
{
    private ContactScoreCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new ContactScoreCalculator([
            new EmailScoreStrategy(),
            new NameScoreStrategy(),
            new PhoneScoreStrategy(),
        ]);
    }

    public function test_calculates_zero_for_minimal_contact(): void
    {
        $score = $this->calculator->calculate(
            new Email('joao@gmail.com'),
            'João',
            '11999999999'
        );

        $this->assertSame(20, $score);
    }

    public function test_calculates_maximum_score(): void
    {
        // corporate(.br) = +30, full name = +10, SP DDD = +20 → total 60
        $score = $this->calculator->calculate(
            new Email('joao@empresa.com.br'),
            'João Silva',
            '11999999999'
        );

        $this->assertSame(60, $score);
    }

    public function test_calculates_score_with_other_state_ddd(): void
    {
        // corporate = +20, full name = +10, other DDD = +10 → total 40
        $score = $this->calculator->calculate(
            new Email('joao@empresa.com'),
            'João Silva',
            '21999999999'
        );

        $this->assertSame(40, $score);
    }

    public function test_calculates_zero_for_non_scoring_contact(): void
    {
        // gmail, single name, SP DDD = +20
        $score = $this->calculator->calculate(
            new Email('joao@gmail.com'),
            'João',
            '11999999999'
        );

        $this->assertSame(20, $score);
    }

    public function test_accepts_empty_strategies(): void
    {
        $calculator = new ContactScoreCalculator([]);

        $score = $calculator->calculate(
            new Email('joao@empresa.com'),
            'João Silva',
            '11999999999'
        );

        $this->assertSame(0, $score);
    }
}