<?php

namespace Tests\Unit\Domain\Strategies;

use App\Domain\Contact\Strategies\EmailScoreStrategy;
use App\Domain\Contact\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class EmailScoreStrategyTest extends TestCase
{
    private EmailScoreStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new EmailScoreStrategy();
    }

    public function test_corporate_email_scores_20_points(): void
    {
        $email = new Email('joao@empresa.com');
        $this->assertSame(20, $this->strategy->calculate($email, '', ''));
    }

    public function test_gmail_scores_zero_points(): void
    {
        $email = new Email('joao@gmail.com');
        $this->assertSame(0, $this->strategy->calculate($email, '', ''));
    }

    public function test_br_email_scores_10_points(): void
    {
        $email = new Email('joao@gmail.com.br');
        $this->assertSame(10, $this->strategy->calculate($email, '', ''));
    }

    public function test_corporate_br_email_scores_30_points(): void
    {
        $email = new Email('joao@empresa.com.br');
        $this->assertSame(30, $this->strategy->calculate($email, '', ''));
    }

    public function test_hotmail_br_scores_only_10_points(): void
    {
        $email = new Email('joao@hotmail.com.br');
        $this->assertSame(10, $this->strategy->calculate($email, '', ''));
    }
}
