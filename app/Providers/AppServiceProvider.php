<?php

namespace App\Providers;

use App\Application\Contracts\ContactRepositoryInterface;
use App\Application\Contact\UseCases\ProcessContactScoreUseCase;
use App\Domain\Contact\Services\ContactScoreCalculator;
use App\Domain\Contact\Strategies\EmailScoreStrategy;
use App\Domain\Contact\Strategies\NameScoreStrategy;
use App\Domain\Contact\Strategies\PhoneScoreStrategy;
use App\Infrastructure\Persistence\Repositories\EloquentContactRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ContactRepositoryInterface::class,
            EloquentContactRepository::class
        );

        $this->app->bind(ContactScoreCalculator::class, function () {
            return new ContactScoreCalculator([
                new EmailScoreStrategy(),
                new NameScoreStrategy(),
                new PhoneScoreStrategy(),
            ]);
        });
    }

    public function boot(): void
    {
        \App\Infrastructure\Persistence\Models\Contact::observe(
            \App\Infrastructure\Persistence\Observers\ContactObserver::class
        );
    }
}
