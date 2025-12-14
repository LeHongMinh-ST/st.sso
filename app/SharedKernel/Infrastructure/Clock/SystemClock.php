<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Clock;

use App\SharedKernel\Domain\Interfaces\ClockInterface;
use App\SharedKernel\Domain\ValueObjects\Timestamp;

/**
 * System clock implementation.
 */
final class SystemClock implements ClockInterface
{
    /**
     * Get current timestamp.
     *
     * @param string|null $timezone
     * @return Timestamp
     */
    public function now(?string $timezone = null): Timestamp
    {
        return Timestamp::now($timezone);
    }
}
