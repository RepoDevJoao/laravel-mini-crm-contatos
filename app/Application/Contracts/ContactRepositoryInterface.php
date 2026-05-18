<?php

namespace App\Application\Contracts;

use App\Domain\Contact\ValueObjects\ContactStatus;

interface ContactRepositoryInterface
{
    public function findById(int $id): ?array;

    public function create(array $data): array;

    public function update(int $id, array $data): array;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15): array;
}