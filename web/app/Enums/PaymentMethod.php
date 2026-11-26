<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cod = 'cod';
    case BankTransfer = 'bank_transfer';

    public function label(): string
    {
        return match ($this) {
            self::Cod => 'Thanh toán khi nhận hàng (COD)',
            self::BankTransfer => 'Chuyển khoản ngân hàng',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
