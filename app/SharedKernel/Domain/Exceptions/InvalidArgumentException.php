<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

use Throwable;

/**
 * Exception thrown when an invalid argument is provided.
 */
final class InvalidArgumentException extends DomainException
{
    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
