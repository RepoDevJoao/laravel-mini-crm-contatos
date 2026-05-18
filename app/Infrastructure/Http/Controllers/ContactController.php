<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\Contact\DTOs\CreateContactDTO;
use App\Application\Contact\DTOs\UpdateContactDTO;
use App\Application\Contact\UseCases\CreateContactUseCase;
use App\Application\Contact\UseCases\DeleteContactUseCase;
use App\Application\Contact\UseCases\ListContactsUseCase;
use App\Application\Contact\UseCases\ProcessContactScoreUseCase;
use App\Application\Contact\UseCases\ShowContactUseCase;
use App\Application\Contact\UseCases\UpdateContactUseCase;
use App\Infrastructure\Http\Requests\StoreContactRequest;
use App\Infrastructure\Http\Requests\UpdateContactRequest;
use App\Infrastructure\Http\Resources\ContactResource;
use App\Infrastructure\Queue\Jobs\ProcessContactScoreJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ContactController extends Controller
{
    public function __construct(
        private readonly CreateContactUseCase  $createUseCase,
        private readonly ListContactsUseCase   $listUseCase,
        private readonly ShowContactUseCase    $showUseCase,
        private readonly UpdateContactUseCase  $updateUseCase,
        private readonly DeleteContactUseCase  $deleteUseCase,
    ) {}

    public function index(): JsonResponse
    {
        $contacts = $this->listUseCase->execute();

        return response()->json($contacts);
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = $this->createUseCase->execute(
            CreateContactDTO::fromArray($request->validated())
        );

        return response()->json(new ContactResource($contact), 201);
    }

    public function show(int $contact): JsonResponse
    {
        $contact = $this->showUseCase->execute($contact);

        return response()->json(new ContactResource($contact));
    }

    public function update(UpdateContactRequest $request, int $contact): JsonResponse
    {
        $updated = $this->updateUseCase->execute(
            $contact,
            UpdateContactDTO::fromArray($request->validated())
        );

        return response()->json(new ContactResource($updated));
    }

    public function destroy(int $contact): JsonResponse
    {
        $this->deleteUseCase->execute($contact);

        return response()->json(null, 204);
    }

    public function processScore(int $contact): JsonResponse
    {
        ProcessContactScoreJob::dispatch($contact);

        return response()->json(['message' => 'Score processing queued.'], 202);
    }
}