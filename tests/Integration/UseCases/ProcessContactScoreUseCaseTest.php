<?php

namespace Tests\Integration\UseCases;

use App\Application\Contact\UseCases\ProcessContactScoreUseCase;
use App\Application\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Services\ContactScoreCalculator;
use App\Domain\Contact\Strategies\EmailScoreStrategy;
use App\Domain\Contact\Strategies\NameScoreStrategy;
use App\Domain\Contact\Strategies\PhoneScoreStrategy;
use App\Domain\Contact\ValueObjects\ContactStatus;
use App\Infrastructure\Broadcasting\Events\ContactScoreProcessed;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProcessContactScoreUseCaseTest extends TestCase
{
    private ContactRepositoryInterface $repository;
    private ProcessContactScoreUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(ContactRepositoryInterface::class);

        $calculator = new ContactScoreCalculator([
            new EmailScoreStrategy(),
            new NameScoreStrategy(),
            new PhoneScoreStrategy(),
        ]);

        $this->useCase = new ProcessContactScoreUseCase(
            $this->repository,
            $calculator
        );
    }

    public function test_processes_score_successfully(): void
    {
        Event::fake();

        $contact = [
            'id'     => 1,
            'name'   => 'João Silva',
            'email'  => 'joao@empresa.com.br',
            'phone'  => '11999999999',
            'score'  => 0,
            'status' => ContactStatus::Pending->value,
        ];

        $this->repository
            ->method('findById')
            ->with(1)
            ->willReturn($contact);

        $this->repository
            ->method('update')
            ->willReturnCallback(function (int $id, array $data) use ($contact) {
                return array_merge($contact, $data);
            });

        $result = $this->useCase->execute(1);

        $this->assertSame(ContactStatus::Active->value, $result['status']);
        $this->assertSame(60, $result['score']);
        Event::assertDispatched(ContactScoreProcessed::class);
    }

    public function test_throws_exception_when_contact_not_found(): void
    {
        $this->repository
            ->method('findById')
            ->with(99)
            ->willReturn(null);

        $this->expectException(ModelNotFoundException::class);

        $this->useCase->execute(99);
    }

    public function test_sets_status_to_failed_on_error(): void
    {
        $contact = [
            'id'     => 1,
            'name'   => 'João Silva',
            'email'  => 'invalid-email',
            'phone'  => '11999999999',
            'score'  => 0,
            'status' => ContactStatus::Pending->value,
        ];

        $this->repository
            ->method('findById')
            ->willReturn($contact);

        $updatedData = [];
        $this->repository
            ->method('update')
            ->willReturnCallback(function (int $id, array $data) use ($contact, &$updatedData) {
                $updatedData = $data;
                return array_merge($contact, $data);
            });

        $this->expectException(\Throwable::class);

        $this->useCase->execute(1);

        $this->assertSame(ContactStatus::Failed->value, $updatedData['status']);
    }
}