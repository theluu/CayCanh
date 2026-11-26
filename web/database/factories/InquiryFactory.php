<?php

namespace Database\Factories;

use App\Enums\InquiryStatus;
use App\Enums\InquiryType;
use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inquiry>
 */
class InquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => InquiryType::Contact,
            'status' => InquiryStatus::New,
            'name' => fake()->name(),
            'phone' => '09'.fake()->numerify('########'),
            'email' => fake()->safeEmail(),
            'message' => fake()->sentence(12),
        ];
    }
}
