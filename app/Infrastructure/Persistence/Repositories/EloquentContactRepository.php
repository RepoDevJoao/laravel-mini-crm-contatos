<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Contracts\ContactRepositoryInterface;
use App\Infrastructure\Persistence\Models\Contact;

class EloquentContactRepository implements ContactRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $contact = Contact::find($id);

        return $contact?->toArray();
    }

    public function create(array $data): array
    {
        return Contact::create($data)->toArray();
    }

    public function update(int $id, array $data): array
    {
        $contact = Contact::findOrFail($id);
        $contact->update($data);

        return $contact->fresh()->toArray();
    }

    public function delete(int $id): bool
    {
        $contact = Contact::findOrFail($id);

        return $contact->delete();
    }

    public function paginate(int $perPage = 15): array
    {
        return Contact::paginate($perPage)->toArray();
    }
}