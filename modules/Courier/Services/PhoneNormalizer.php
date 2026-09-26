<?php

namespace Modules\Courier\Services;

/**
 * Normalizes Bangladeshi phone numbers into the 11-digit local format
 * (01XXXXXXXXX) that courier APIs require. Returns an empty string when the
 * input cannot be interpreted as a BD mobile number.
 */
class PhoneNormalizer
{
    public static function normalize(mixed $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $phone) ?? '';

        if (str_starts_with($digits, '880') && strlen($digits) === 13) {
            $digits = '0'.substr($digits, 3);
        }

        return $digits;
    }
}
