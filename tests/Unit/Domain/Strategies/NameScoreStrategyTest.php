<?php

namespace Tests\Unit\Domain\Strategies;

use App\Domain\Contact\Strategies\NameScoreStrategy;
use App\Domain\Contact\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class NameScoreStrategyTest extends TestCase
{
    private NameScoreStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new NameScoreStrategy();
    }

    public function test_full_name_scores_10_points(): void
    {
        $email = new Email('joao@empresa.com');
        $this->assertSame(10, $this->strategy->calculate($email, 'João Silva', ''));
    }

    public function test_single_name_scores_zero_points(): void
    {
        $email = new Email('joao@empresa.com');
        $this->assertSame(0, $this->strategy->calculate($email, 'João', ''));
    }

    public function test_name_with_multiple_words_scores_10_points(): void
    {
        $email = new Email('joao@empresa.com');
        $this->assertSame(10, $this->strategy->calculate($email, 'João da Silva Souza', ''));
    }

    public function test_empty_name_scores_zero_points(): void
    {
        $email = new Email('joao@empresa.com');
        $this->assertSame(0, $this->strategy->calculate($email, '', ''));
    }
}
