<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use App\Enums\InquiryType;
use Database\Factories\InquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['type', 'status', 'name', 'phone', 'email', 'address', 'message', 'subject_type', 'subject_id', 'meta', 'assigned_to', 'internal_note', 'ip'])]
class Inquiry extends Model
{
    /** @use HasFactory<InquiryFactory> */
    use HasFactory;

    /** Public form values for subject_type mapped to morph aliases. */
    public const SUBJECTS = ['product', 'service', 'package'];

    protected $attributes = [
        'status' => 'new',
    ];

    protected function casts(): array
    {
        return [
            'type' => InquiryType::class,
            'status' => InquiryStatus::class,
            'meta' => 'array',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function subjectLabel(): ?string
    {
        $subject = $this->subject;

        return match (true) {
            $subject instanceof ServicePackage => $subject->service?->name.' – '.$subject->name,
            $subject instanceof Product, $subject instanceof Service => $subject->name,
            default => null,
        };
    }
}
