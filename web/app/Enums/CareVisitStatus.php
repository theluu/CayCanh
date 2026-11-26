<?php

namespace App\Enums;

enum CareVisitStatus: string
{
    case Scheduled = 'scheduled';
    case Done = 'done';
    case Cancelled = 'cancelled';
    case Rescheduled = 'rescheduled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Đã lên lịch',
            self::Done => 'Hoàn thành',
            self::Cancelled => 'Huỷ',
            self::Rescheduled => 'Dời lịch',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'info',
            self::Done => 'success',
            self::Cancelled => 'gray',
            self::Rescheduled => 'warning',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
