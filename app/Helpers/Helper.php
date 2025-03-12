
<?php

namespace App\Helpers;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;

class Helper
{


    public static function isValidDateFormat($dateString, $format): bool
    {
        try {
            $date = Carbon::createFromFormat($format, $dateString);

            return $date && $date->format($format) === $dateString;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

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
