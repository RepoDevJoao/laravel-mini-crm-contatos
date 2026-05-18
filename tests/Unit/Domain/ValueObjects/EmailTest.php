<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\Contact\ValueObjects\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_can_create_valid_email(): void
    {
        $email = new Email('joao@empresa.com.br');
        $this->assertSame('joao@empresa.com.br', $email->value());
    }

    public function test_throws_exception_for_invalid_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('not-an-email');
    }

    public function test_corporate_email_is_not_gmail(): void
    {
        $email = new Email('joao@empresa.com');
        $this->assertTrue($email->isCorporate());
    }

    public function test_gmail_is_not_corporate(): void
    {
        $email = new Email('joao@gmail.com');
        $this->assertFalse($email->isCorporate());
    }

    public function test_hotmail_is_not_corporate(): void
    {
        $email = new Email('joao@hotmail.com');
        $this->assertFalse($email->isCorporate());
    }

    public function test_yahoo_is_not_corporate(): void
    {
        $email = new Email('joao@yahoo.com');
        $this->assertFalse($email->isCorporate());
    }

    public function test_email_ending_with_br_is_detected(): void
    {
        $email = new Email('joao@empresa.com.br');
        $this->assertTrue($email->isFromBrazil());
    }

    public function test_email_not_ending_with_br_is_detected(): void
    {
        $email = new Email('joao@empresa.com');
        $this->assertFalse($email->isFromBrazil());
    }

    public function test_corporate_br_email_accumulates_both_bonuses(): void
    {
        $email = new Email('joao@empresa.com.br');
        $this->assertTrue($email->isCorporate());
        $this->assertTrue($email->isFromBrazil());
    }
}
