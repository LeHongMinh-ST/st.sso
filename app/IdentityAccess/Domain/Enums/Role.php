<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Enums;

/**
 * Role enumeration for IdentityAccess context.
 */
enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Officer = 'officer';
    case Student = 'student';
    case Normal = 'normal';

    /**
     * Get human-readable descriptions for each role.
     *
     * @return array<string, string>
     */
    public static function getDescription(): array
    {
        return [
            self::SuperAdmin->value => 'Quản trị viên',
            self::Officer->value => 'Giảng viên - Cán bộ khoa',
            self::Student->value => 'Sinh viên',
            self::Normal->value => 'Cơ bản',
        ];
    }

    /**
     * Get label for the current role.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return self::getDescription()[$this->value];
    }
}
