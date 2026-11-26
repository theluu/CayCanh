<?php

namespace Database\Factories;

use App\Enums\CareFrequency;
use App\Models\CareContract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CareContract>
 */
class CareContractFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_name' => fake()->company(),
            'phone' => '09'.fake()->numerify('########'),
            'address' => fake()->streetAddress(),
            'starts_on' => today(),
            'ends_on' => null,
            'frequency' => CareFrequency::Weekly,
            'is_active' => true,
        ];
    }
}
