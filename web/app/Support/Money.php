<?php

namespace App\Support;

class Money
{
    public static function vnd(int $amount): string
    {
        return number_format($amount, 0, ',', '.').' ₫';
    }
}
