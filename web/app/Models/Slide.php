<?php

namespace App\Models;

use App\Models\Concerns\HasImageConversions;
use Database\Factories\SlideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

#[Fillable(['title', 'subtitle', 'button_text', 'button_url', 'sort', 'is_active'])]
class Slide extends Model implements HasMedia
{
    /** @use HasFactory<SlideFactory> */
    use HasFactory, HasImageConversions;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort');
    }
}
