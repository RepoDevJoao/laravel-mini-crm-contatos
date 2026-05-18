<?php

namespace App\Providers;

use App\Infrastructure\Broadcasting\Events\ContactScoreProcessed;
use App\Infrastructure\Broadcasting\Listeners\LogContactScoreListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ContactScoreProcessed::class => [
            LogContactScoreListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}