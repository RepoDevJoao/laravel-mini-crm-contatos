<?php

namespace App\Infrastructure\Broadcasting\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactScoreProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly array $contact
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('contacts.' . $this->contact['id']);
    }

    public function broadcastAs(): string
    {
        return 'score.processed';
    }
}