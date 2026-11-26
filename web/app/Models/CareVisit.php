<?php

namespace App\Models;

use App\Enums\CareVisitStatus;
use App\Models\Concerns\HasImageConversions;
use Database\Factories\CareVisitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;

#[Fillable(['care_contract_id', 'scheduled_on', 'technician_id', 'status', 'note'])]
class CareVisit extends Model implements HasMedia
{
    /** @use HasFactory<CareVisitFactory> */
    use HasFactory, HasImageConversions;

    protected $attributes = [
        'status' => 'scheduled',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_on' => 'immutable_date',
            'status' => CareVisitStatus::class,
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('before');
        $this->addMediaCollection('after');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(CareContract::class, 'care_contract_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
