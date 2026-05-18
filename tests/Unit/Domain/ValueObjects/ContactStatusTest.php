<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\Contact\ValueObjects\ContactStatus;
use PHPUnit\Framework\TestCase;

class ContactStatusTest extends TestCase
{
    public function test_can_create_valid_statuses(): void
    {
        $this->assertSame('pending', ContactStatus::Pending->value);
        $this->assertSame('processing', ContactStatus::Processing->value);
        $this->assertSame('active', ContactStatus::Active->value);
        $this->assertSame('failed', ContactStatus::Failed->value);
    }

    public function test_can_create_from_string(): void
    {
        $status = ContactStatus::from('pending');
        $this->assertSame(ContactStatus::Pending, $status);
    }

    public function test_throws_exception_for_invalid_status(): void
    {
        $this->expectException(\ValueError::class);
        ContactStatus::from('invalid');
    }

    public function test_is_terminal_returns_true_for_active_and_failed(): void
    {
        $this->assertTrue(ContactStatus::Active->isTerminal());
        $this->assertTrue(ContactStatus::Failed->isTerminal());
    }

    public function test_is_terminal_returns_false_for_pending_and_processing(): void
    {
        $this->assertFalse(ContactStatus::Pending->isTerminal());
        $this->assertFalse(ContactStatus::Processing->isTerminal());
    }
}
