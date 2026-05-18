<?php

namespace App\Application\Contact\UseCases;

use App\Application\Contracts\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShowContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ) {}

    public function execute(int $id): array
    {
        $contact = $this->repository->findById($id);

        if (!$contact) {
            throw new ModelNotFoundException("Contact {$id} not found.");
        }

        return $contact;
    }
}