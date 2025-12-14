<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;

/**
 * Full name value object.
 * Represents a person's full name (first name + last name).
 */
final class FullName
{
    private string $firstName;

    private string $lastName;

    private function __construct(string $firstName, string $lastName)
    {
        $this->validate($firstName, $lastName);
        $this->firstName = trim($firstName);
        $this->lastName = trim($lastName);
    }

    /**
     * Get full name string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->fullName();
    }

    /**
     * Create FullName from first name and last name parts.
     *
     * @param string $firstName First name
     * @param string $lastName Last name
     * @return self
     */
    public static function fromParts(string $firstName, string $lastName): self
    {
        return new self($firstName, $lastName);
    }

    /**
     * Get first name.
     *
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * Get full name in format "Last Name First Name".
     *
     * @return string
     */
    public function fullName(): string
    {
        return "{$this->lastName} {$this->firstName}";
    }

    /**
     * Check if this FullName equals another FullName.
     *
     * @param FullName $other
     * @return bool
     */
    public function equals(FullName $other): bool
    {
        return $this->firstName === $other->firstName
            && $this->lastName === $other->lastName;
    }

    /**
     * Validate first name and last name.
     *
     * @param string $firstName
     * @param string $lastName
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $firstName, string $lastName): void
    {
        if (empty(trim($firstName))) {
            throw new InvalidArgumentException('First name cannot be empty');
        }

        if (empty(trim($lastName))) {
            throw new InvalidArgumentException('Last name cannot be empty');
        }

        if (mb_strlen(trim($firstName)) > 255) {
            throw new InvalidArgumentException('First name cannot exceed 255 characters');
        }

        if (mb_strlen(trim($lastName)) > 255) {
            throw new InvalidArgumentException('Last name cannot exceed 255 characters');
        }
    }
}
