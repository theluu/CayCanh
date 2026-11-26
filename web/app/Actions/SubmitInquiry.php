<?php

namespace App\Actions;

use App\Events\InquirySubmitted;
use App\Models\Inquiry;
use App\Rules\VietnamesePhone;

class SubmitInquiry
{
    /**
     * @param  array<string, mixed>  $data  validated form data
     */
    public function handle(array $data, ?string $ip = null): Inquiry
    {
        $meta = array_filter((array) ($data['meta'] ?? []), fn ($value) => filled($value));

        $inquiry = Inquiry::create([
            'type' => $data['type'],
            'name' => $data['name'],
            'phone' => VietnamesePhone::normalize($data['phone']),
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'message' => $data['message'] ?? null,
            'subject_type' => filled($data['subject_type'] ?? null) ? $data['subject_type'] : null,
            'subject_id' => filled($data['subject_type'] ?? null) ? $data['subject_id'] : null,
            'meta' => $meta ?: null,
            'ip' => $ip,
        ]);

        InquirySubmitted::dispatch($inquiry);

        return $inquiry;
    }
}
