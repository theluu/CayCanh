<?php

namespace App\Enums;

enum InquiryType: string
{
    case Contact = 'contact';
    case QuoteProduct = 'quote_product';
    case QuoteService = 'quote_service';
    case Survey = 'survey';

    public function label(): string
    {
        return match ($this) {
            self::Contact => 'Liên hệ',
            self::QuoteProduct => 'Báo giá sản phẩm / cây',
            self::QuoteService => 'Báo giá dịch vụ',
            self::Survey => 'Khảo sát công trình',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
