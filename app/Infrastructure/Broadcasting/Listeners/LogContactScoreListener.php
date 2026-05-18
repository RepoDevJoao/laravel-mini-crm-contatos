<?php

namespace App\Infrastructure\Broadcasting\Listeners;

use App\Infrastructure\Broadcasting\Events\ContactScoreProcessed;
use Illuminate\Support\Facades\Log;

class LogContactScoreListener
{
    public function handle(ContactScoreProcessed $event): void
    {
        Log::channel('contact')->info('Contact score processed', [
            'id'     => $event->contact['id'],
            'email'  => $event->contact['email'],
            'score'  => $event->contact['score'],
            'status' => $event->contact['status'],
        ]);
    }
}