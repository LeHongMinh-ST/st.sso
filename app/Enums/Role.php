<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Officer = 'officer';
    case Teacher = 'teacher';
    case Student = 'student';
    case Normal = 'normal';

    public static function getDescription()
    {
        return [
            self::SuperAdmin->value => 'Quản trị hệ thống',
            self::Officer->value => 'Cán bộ khoa',
            self::Teacher->value => 'Giáo viên',
            self::Student->value => 'Học sinh',
            self::Normal->value => 'Người dùng khác',
        ];
    }
}
