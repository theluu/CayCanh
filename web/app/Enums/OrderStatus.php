<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Confirmed = 'confirmed';
    case Shipping = 'shipping';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Mới',
            self::Contacted => 'Đã liên hệ',
            self::Confirmed => 'Đã chốt',
            self::Shipping => 'Đang giao',
            self::Completed => 'Hoàn thành',
            self::Cancelled => 'Huỷ',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'danger',
            self::Contacted => 'warning',
            self::Confirmed => 'info',
            self::Shipping => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'gray',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
