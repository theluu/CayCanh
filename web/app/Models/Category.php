<?php

namespace App\Models;

use App\Enums\ProductKind;
use App\Models\Concerns\HasSlug;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['parent_id', 'scope', 'name', 'slug', 'description', 'sort', 'is_visible'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, HasSlug;

    protected function casts(): array
    {
        return [
            'scope' => ProductKind::class,
            'is_visible' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_visible', true)->orderBy('sort')->orderBy('name');
    }

    public function scopeForKind(Builder $query, ProductKind $kind): void
    {
        $query->where('scope', $kind);
    }
}
