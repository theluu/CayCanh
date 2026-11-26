<?php

namespace App\Enums;

use Carbon\CarbonImmutable;

enum CareFrequency: string
{
    case Weekly = 'weekly';
    case Biweekly = 'biweekly';
    case Monthly = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::Weekly => 'Hằng tuần',
            self::Biweekly => '2 tuần / lần',
            self::Monthly => 'Hằng tháng',
        };
    }

    /** The date of the n-th visit (0-based) counted from the start date, never drifting at month ends. */
    public function occurrence(CarbonImmutable $start, int $index): CarbonImmutable
    {
        return match ($this) {
            self::Weekly => $start->addWeeks($index),
            self::Biweekly => $start->addWeeks($index * 2),
            self::Monthly => $start->addMonthsNoOverflow($index),
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
