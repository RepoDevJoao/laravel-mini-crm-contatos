<?php

namespace Tests\Unit\Domain\Strategies;

use App\Domain\Contact\Strategies\PhoneScoreStrategy;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;
use PHPUnit\Framework\TestCase;

class PhoneScoreStrategyTest extends TestCase
{
    private PhoneScoreStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new PhoneScoreStrategy();
    }

    public function test_sao_paulo_ddd_scores_20_points(): void
    {
        $email = new Email('joao@empresa.com');
        $phone = new Phone('(11) 99999-9999');
        $this->assertSame(20, $this->strategy->calculate($email, '', $phone->value()));
    }

    public function test_other_state_ddd_scores_10_points(): void
    {
        $email = new Email('joao@empresa.com');
        $phone = new Phone('(21) 99999-9999');
        $this->assertSame(10, $this->strategy->calculate($email, '', $phone->value()));
    }

    public function test_ddd_19_scores_20_points(): void
    {
        $email = new Email('joao@empresa.com');
        $phone = new Phone('(19) 99999-9999');
        $this->assertSame(20, $this->strategy->calculate($email, '', $phone->value()));
    }

    public function test_ddd_15_scores_20_points(): void
    {
        $email = new Email('joao@empresa.com');
        $phone = new Phone('(15) 99999-9999');
        $this->assertSame(20, $this->strategy->calculate($email, '', $phone->value()));
    }

    public function test_empty_phone_scores_zero_points(): void
    {
        $email = new Email('joao@empresa.com');
        $this->assertSame(0, $this->strategy->calculate($email, '', ''));
    }
}