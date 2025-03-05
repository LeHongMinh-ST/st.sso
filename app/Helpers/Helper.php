<?php

namespace App\Helpers;

class Helper
{
    public static function splitFullName($fullName)
    {
        $parts = explode(' ', trim($fullName));

        if (count($parts) === 1) {
            return ['last_name' => $parts[0], 'first_name' => ''];
        }

        $lastName = array_shift($parts);
        $firstName = implode(' ', $parts);

        return ['last_name' => $lastName, 'first_name' => $firstName];
    }
}
