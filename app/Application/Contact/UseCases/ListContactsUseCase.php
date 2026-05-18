<?php

namespace App\Application\Contact\UseCases;

use App\Application\Contracts\ContactRepositoryInterface;

class ListContactsUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ) {}

    public function execute(int $perPage = 15): array
    {
        return $this->repository->paginate($perPage);
    }
}