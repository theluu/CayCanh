<?php

namespace App\Enums;

enum ProductKind: string
{
    case Standard = 'standard';
    case Art = 'art';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Sản phẩm',
            self::Art => 'Cây cảnh nghệ thuật',
        };
    }

    public function publicPathPrefix(): string
    {
        return match ($this) {
            self::Standard => '/san-pham',
            self::Art => '/cay-canh-nghe-thuat',
        };
    }
}
