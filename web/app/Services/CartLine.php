<?php

namespace App\Services;

use App\Models\Product;

final readonly class CartLine
{
    public function __construct(
        public Product $product,
        public int $qty,
        public int $unitPrice,
    ) {}

    public function lineTotal(): int
    {
        return $this->unitPrice * $this->qty;
    }
}
