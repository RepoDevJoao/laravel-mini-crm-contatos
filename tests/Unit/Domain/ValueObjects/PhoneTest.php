<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\Contact\ValueObjects\Phone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function test_can_create_valid_phone(): void
    {
        $phone = new Phone('(11) 99999-9999');
        $this->assertSame('11999999999', $phone->value());
    }

    public function test_normalizes_phone_removing_mask(): void
    {
        $phone = new Phone('(11) 3333-4444');
        $this->assertSame('1133334444', $phone->value());
    }

    public function test_throws_exception_for_phone_without_ddd(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Phone('99999-9999');
    }

    public function test_throws_exception_for_too_short_phone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Phone('123');
    }

    public function test_sao_paulo_ddd_11_is_detected(): void
    {
        $phone = new Phone('(11) 99999-9999');
        $this->assertTrue($phone->isSaoPauloDdd());
    }

    public function test_sao_paulo_ddd_19_is_detected(): void
    {
        $phone = new Phone('(19) 99999-9999');
        $this->assertTrue($phone->isSaoPauloDdd());
    }

    public function test_sao_paulo_ddd_15_is_detected(): void
    {
        $phone = new Phone('(15) 99999-9999');
        $this->assertTrue($phone->isSaoPauloDdd());
    }

    public function test_other_state_ddd_21_is_not_sao_paulo(): void
    {
        $phone = new Phone('(21) 99999-9999');
        $this->assertFalse($phone->isSaoPauloDdd());
    }

    public function test_other_state_ddd_is_valid(): void
    {
        $phone = new Phone('(21) 99999-9999');
        $this->assertTrue($phone->hasValidDdd());
    }

    public function test_sao_paulo_ddd_also_has_valid_ddd(): void
    {
        $phone = new Phone('(11) 99999-9999');
        $this->assertTrue($phone->hasValidDdd());
    }
}
