<?php

namespace App\Models;

use App\Enums\ArtAvailability;
use App\Enums\ProductKind;
use App\Models\Concerns\HasImageConversions;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Support\Money;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;

#[Fillable([
    'category_id', 'kind', 'name', 'slug', 'sku', 'price', 'sale_price', 'show_price', 'availability',
    'in_stock', 'summary', 'description', 'specs', 'video_url', 'is_featured', 'is_published',
    'published_at', 'reserved_inquiry_id', 'seo_title', 'seo_description',
])]
class Product extends Model implements HasMedia
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasImageConversions, HasSeo, HasSlug;

    protected $attributes = [
        'kind' => 'standard',
        'availability' => 'available',
        'in_stock' => true,
        'is_featured' => false,
        'is_published' => true,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $product) {
            if (! array_key_exists('show_price', $product->getAttributes())) {
                $product->show_price = $product->kind !== ProductKind::Art;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'kind' => ProductKind::class,
            'availability' => ArtAvailability::class,
            'price' => 'integer',
            'sale_price' => 'integer',
            'show_price' => 'boolean',
            'in_stock' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'specs' => 'array',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reservedInquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class, 'reserved_inquiry_id');
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeStandard(Builder $query): void
    {
        $query->where('kind', ProductKind::Standard);
    }

    public function scopeArt(Builder $query): void
    {
        $query->where('kind', ProductKind::Art);
    }

    public function isArt(): bool
    {
        return $this->kind === ProductKind::Art;
    }

    public function isPublished(): bool
    {
        return $this->is_published && ($this->published_at === null || $this->published_at->isPast());
    }

    public function isPurchasable(): bool
    {
        return $this->kind === ProductKind::Standard
            && $this->isPublished()
            && $this->in_stock
            && $this->effectivePrice() > 0;
    }

    public function effectivePrice(): ?int
    {
        if ($this->price === null) {
            return null;
        }

        if ($this->sale_price !== null && $this->sale_price > 0 && $this->sale_price < $this->price) {
            return $this->sale_price;
        }

        return $this->price;
    }

    public function displayPrice(): string
    {
        $price = $this->effectivePrice();

        if (! $this->show_price || ! $price) {
            return 'Giá liên hệ';
        }

        return Money::vnd($price);
    }

    public function publicPathPrefix(): ?string
    {
        return ($this->kind ?? ProductKind::Standard)->publicPathPrefix();
    }
}
