<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case New = 'new';
    case Processing = 'processing';
    case Quoted = 'quoted';
    case Won = 'won';
    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Mới',
            self::Processing => 'Đang xử lý',
            self::Quoted => 'Đã báo giá',
            self::Won => 'Thành công',
            self::Lost => 'Không thành công',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'danger',
            self::Processing => 'warning',
            self::Quoted => 'info',
            self::Won => 'success',
            self::Lost => 'gray',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
