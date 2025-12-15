<?php

declare(strict_types=1);

namespace App\Enums;

use App\IdentityAccess\Domain\Enums\Role as IdentityRole;

/**
 * @deprecated Use App\IdentityAccess\Domain\Enums\Role instead.
 * Temporary shim to maintain backward compatibility while migrating.
 */
enum Role: string
{
    case SuperAdmin = IdentityRole::SuperAdmin->value;
    case Officer = IdentityRole::Officer->value;
    case Student = IdentityRole::Student->value;
    case Normal = IdentityRole::Normal->value;

    /**
     * Get descriptions via IdentityAccess Role enum.
     *
     * @return array<string, string>
     */
    public static function getDescription(): array
    {
        return IdentityRole::getDescription();
    }

    /**
     * Get label for the current role.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return IdentityRole::from($this->value)->getLabel();
    }
}
