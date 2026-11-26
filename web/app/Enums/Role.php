<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'quan-tri-vien';
    case Editor = 'bien-tap-vien';
    case Sales = 'kinh-doanh';
    case Technician = 'ky-thuat-vien';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Quản trị viên',
            self::Editor => 'Biên tập viên',
            self::Sales => 'Kinh doanh',
            self::Technician => 'Kỹ thuật viên',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $role) => [$role->value => $role->label()])->all();
    }
}
