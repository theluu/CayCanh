<?php

namespace App\Actions;

use App\Enums\ArtAvailability;
use App\Models\Product;

class ReleaseArtTree
{
    public function handle(Product $product): void
    {
        $product->update(['availability' => ArtAvailability::Available, 'reserved_inquiry_id' => null]);
    }
}
