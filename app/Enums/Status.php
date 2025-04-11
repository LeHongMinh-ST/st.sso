<?php

declare(strict_types=1);

namespace App\Enums;

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public static function getDescription()
    {
        return [
            self::Active->value => 'Hoạt động',
            self::Inactive->value => 'Ẩn',
        ];
    }

    public function getLabel(): string
    {
        return match($this) {
            self::Active => 'Hoạt động',
            self::Inactive => 'Ẩn',
        };
    }
}
