<?php

namespace App\Application\Contact\UseCases;

use App\Application\Contracts\ContactRepositoryInterface;
use App\Application\Contact\DTOs\CreateContactDTO;
use App\Domain\Contact\ValueObjects\ContactStatus;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;

class CreateContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ) {}

    public function execute(CreateContactDTO $dto): array
    {
        new Email($dto->email);
        new Phone($dto->phone);

        return $this->repository->create([
            'name'   => $dto->name,
            'email'  => $dto->email,
            'phone'  => $dto->phone,
            'score'  => 0,
            'status' => ContactStatus::Pending->value,
        ]);
    }
}