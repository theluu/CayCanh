<?php

namespace App\Actions;

use App\Enums\ArtAvailability;
use App\Models\Product;

class MarkArtSold
{
    public function handle(Product $product): void
    {
        $product->update(['availability' => ArtAvailability::Sold]);
    }
}
