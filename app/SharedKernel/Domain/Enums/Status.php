<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Enums;

/**
 * Status enum that can be used across all bounded contexts.
 * Represents active/inactive status for entities.
 */
enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    /**
     * Get all status descriptions.
     *
     * @return array<string, string>
     */
    public static function getDescription(): array
    {
        return [
            self::Active->value => 'Hoạt động',
            self::Inactive->value => 'Ẩn',
        ];
    }

    /**
     * Get label for current status.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::Active => 'Hoạt động',
            self::Inactive => 'Ẩn',
        };
    }
}
