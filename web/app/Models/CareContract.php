<?php

namespace App\Models;

use App\Enums\CareFrequency;
use Database\Factories\CareContractFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['customer_name', 'phone', 'address', 'service_package_id', 'inquiry_id', 'starts_on', 'ends_on', 'frequency', 'default_technician_id', 'note', 'is_active'])]
class CareContract extends Model
{
    /** @use HasFactory<CareContractFactory> */
    use HasFactory;

    protected $attributes = [
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'immutable_date',
            'ends_on' => 'immutable_date',
            'frequency' => CareFrequency::class,
            'is_active' => 'boolean',
        ];
    }

    public function visits(): HasMany
    {
        return $this->hasMany(CareVisit::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function defaultTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'default_technician_id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('ends_on')->orWhere('ends_on', '>=', today()));
    }
}
