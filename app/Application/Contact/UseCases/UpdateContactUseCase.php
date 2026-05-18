<?php

namespace App\Application\Contact\UseCases;

use App\Application\Contracts\ContactRepositoryInterface;
use App\Application\Contact\DTOs\UpdateContactDTO;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;

class UpdateContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ) {}

    public function execute(int $id, UpdateContactDTO $dto): array
    {
        new Email($dto->email);
        new Phone($dto->phone);

        return $this->repository->update($id, [
            'name'  => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
        ]);
    }
}