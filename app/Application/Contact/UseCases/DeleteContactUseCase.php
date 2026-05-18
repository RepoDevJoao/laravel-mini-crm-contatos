<?php

namespace App\Application\Contact\UseCases;

use App\Application\Contracts\ContactRepositoryInterface;

class DeleteContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}