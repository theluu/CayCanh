<?php

namespace App\Models;

use App\Support\Money;
use Database\Factories\ServicePackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['service_id', 'name', 'price_from', 'unit', 'features', 'is_highlighted', 'sort'])]
class ServicePackage extends Model
{
    /** @use HasFactory<ServicePackageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price_from' => 'integer',
            'features' => 'array',
            'is_highlighted' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function displayPrice(): string
    {
        return $this->price_from ? 'Từ '.Money::vnd($this->price_from) : 'Liên hệ';
    }
}
