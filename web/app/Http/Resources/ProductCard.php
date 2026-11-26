<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductCard extends JsonResource
{
    public function toArray(Request $request): array
    {
        $price = $this->effectivePrice();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => $this->publicPath(),
            'kind' => $this->kind->value,
            'summary' => $this->summary,
            'price' => $this->displayPrice(),
            'originalPrice' => $this->show_price && $price !== null && $price < (int) $this->price ? Money::vnd((int) $this->price) : null,
            'availability' => $this->kind->value === 'art' ? ['value' => $this->availability->value, 'label' => $this->availability->label()] : null,
            'canAddToCart' => $this->isPurchasable(),
            'image' => $this->imageUrls('gallery'),
        ];
    }
}
