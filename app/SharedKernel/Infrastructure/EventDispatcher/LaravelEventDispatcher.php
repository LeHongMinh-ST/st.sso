<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\EventDispatcher;

use App\SharedKernel\Domain\Interfaces\EventDispatcherInterface;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Laravel implementation of EventDispatcherInterface.
 */
final class LaravelEventDispatcher implements EventDispatcherInterface
{
    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        private Dispatcher $dispatcher
    ) {
    }

    /**
     * Dispatch a domain event.
     *
     * @param object $event
     * @return void
     */
    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    /**
     * Dispatch multiple domain events.
     *
     * @param array<object> $events
     * @return void
     */
    public function dispatchMany(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
