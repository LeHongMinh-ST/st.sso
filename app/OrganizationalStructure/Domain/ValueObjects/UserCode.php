<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;

/**
 * User code value object.
 * Represents a unique user identifier code (e.g., student code, employee code).
 */
final class UserCode
{
    private string $code;

    private function __construct(string $code)
    {
        $this->validate($code);
        $this->code = trim($code);
    }

    /**
     * Get code string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create UserCode from string.
     *
     * @param string $code Code string
     * @return self
     */
    public static function fromString(string $code): self
    {
        return new self($code);
    }

    /**
     * Check if this UserCode equals another UserCode.
     *
     * @param UserCode $other
     * @return bool
     */
    public function equals(UserCode $other): bool
    {
        return $this->code === $other->code;
    }

    /**
     * Get code string representation.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->code;
    }

    /**
     * Validate user code.
     *
     * @param string $code
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $code): void
    {
        $trimmed = trim($code);

        if (empty($trimmed)) {
            throw new InvalidArgumentException('User code cannot be empty');
        }

        if (mb_strlen($trimmed) > 255) {
            throw new InvalidArgumentException('User code cannot exceed 255 characters');
        }

        // Code should be alphanumeric
        if (!preg_match('/^[A-Za-z0-9]+$/', $trimmed)) {
            throw new InvalidArgumentException('User code must be alphanumeric');
        }
    }
}
