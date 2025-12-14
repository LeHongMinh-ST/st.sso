<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Interfaces;

use App\SharedKernel\Domain\ValueObjects\Timestamp;

/**
 * Interface for getting current time.
 * Useful for testing and ensuring time consistency.
 */
interface ClockInterface
{
    /**
     * Get current timestamp.
     *
     * @param string|null $timezone
     * @return Timestamp
     */
    public function now(?string $timezone = null): Timestamp;
}
