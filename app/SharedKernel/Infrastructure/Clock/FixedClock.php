<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Clock;

use App\SharedKernel\Domain\Interfaces\ClockInterface;
use App\SharedKernel\Domain\ValueObjects\Timestamp;

/**
 * Fixed clock implementation for testing.
 * Always returns the same timestamp.
 */
final class FixedClock implements ClockInterface
{
    private Timestamp $fixedTime;

    /**
     * @param Timestamp $fixedTime
     */
    public function __construct(Timestamp $fixedTime)
    {
        $this->fixedTime = $fixedTime;
    }

    /**
     * Get fixed timestamp.
     *
     * @param string|null $timezone
     * @return Timestamp
     */
    public function now(?string $timezone = null): Timestamp
    {
        return $this->fixedTime;
    }
}
