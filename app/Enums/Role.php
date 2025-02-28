<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Officer = 'officer';
    case Teacher = 'teacher';
    case Student = 'student';
}