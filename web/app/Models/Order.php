<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Support\Money;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['code', 'customer_name', 'phone', 'email', 'province', 'address', 'note', 'payment_method', 'status', 'subtotal', 'admin_note'])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'new',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
            'subtotal' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function formattedSubtotal(): string
    {
        return Money::vnd($this->subtotal);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'DH-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
