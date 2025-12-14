<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Interfaces;

/**
 * Interface for dispatching domain events.
 * This allows easy mocking in tests and decoupling from Laravel's event system.
 */
interface EventDispatcherInterface
{
    /**
     * Dispatch a domain event.
     *
     * @param object $event
     * @return void
     */
    public function dispatch(object $event): void;

    /**
     * Dispatch multiple domain events.
     *
     * @param array<object> $events
     * @return void
     */
    public function dispatchMany(array $events): void;
}
