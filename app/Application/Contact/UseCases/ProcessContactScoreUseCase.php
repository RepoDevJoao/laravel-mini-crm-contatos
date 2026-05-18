<?php

namespace App\Application\Contact\UseCases;

use App\Application\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Services\ContactScoreCalculator;
use App\Domain\Contact\ValueObjects\ContactStatus;
use App\Domain\Contact\ValueObjects\Email;
use App\Infrastructure\Broadcasting\Events\ContactScoreProcessed;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProcessContactScoreUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository,
        private readonly ContactScoreCalculator     $calculator
    ) {}

    public function execute(int $id): array
    {
        $contact = $this->repository->findById($id);

        if (!$contact) {
            throw new ModelNotFoundException("Contact {$id} not found.");
        }

        $this->repository->update($id, [
            'status' => ContactStatus::Processing->value,
        ]);

        try {
            $email = new Email($contact['email']);
            $score = $this->calculator->calculate(
                $email,
                $contact['name'],
                $contact['phone']
            );

            sleep(1);

            $updated = $this->repository->update($id, [
                'score'        => $score,
                'status'       => ContactStatus::Active->value,
                'processed_at' => now(),
            ]);

            event(new ContactScoreProcessed($updated));

            return $updated;
        } catch (\Throwable $e) {
            $this->repository->update($id, [
                'status' => ContactStatus::Failed->value,
            ]);

            throw $e;
        }
    }
}