<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VietnamesePhone implements ValidationRule
{
    private const PATTERN = '/^(0|\+84|84)(3|5|7|8|9)\d{8}$/';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match(self::PATTERN, self::strip($value))) {
            $fail('Số điện thoại không hợp lệ.');
        }
    }

    /** Converts any accepted format (0912 345 678, +84912345678, 84-912-345-678) to 0912345678. */
    public static function normalize(string $phone): string
    {
        return '0'.substr(self::strip($phone), -9);
    }

    private static function strip(string $phone): string
    {
        return preg_replace('/[\s.\-()]/', '', $phone) ?? '';
    }
}
