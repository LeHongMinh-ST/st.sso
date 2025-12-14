<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Providers;

use App\SharedKernel\Domain\Interfaces\ClockInterface;
use App\SharedKernel\Domain\Interfaces\EventDispatcherInterface;
use App\SharedKernel\Infrastructure\Clock\SystemClock;
use App\SharedKernel\Infrastructure\EventDispatcher\LaravelEventDispatcher;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for Shared Kernel.
 */
class SharedKernelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind EventDispatcherInterface
        $this->app->singleton(EventDispatcherInterface::class, function ($app) {
            return new LaravelEventDispatcher($app->make(Dispatcher::class));
        });

        // Bind ClockInterface
        $this->app->singleton(ClockInterface::class, SystemClock::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
