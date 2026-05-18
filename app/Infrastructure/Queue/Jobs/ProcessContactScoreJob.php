<?php

namespace App\Infrastructure\Queue\Jobs;

use App\Application\Contact\UseCases\ProcessContactScoreUseCase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessContactScoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $contactId
    ) {}

    public function handle(ProcessContactScoreUseCase $useCase): void
    {
        $useCase->execute($this->contactId);
    }
}