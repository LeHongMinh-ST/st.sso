<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

use RuntimeException;

/**
 * Base exception for domain layer.
 * All domain-specific exceptions should extend this class.
 */
class DomainException extends RuntimeException
{
    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
