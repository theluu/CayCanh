<?php

namespace Database\Factories;

use App\Enums\CareVisitStatus;
use App\Models\CareContract;
use App\Models\CareVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CareVisit>
 */
class CareVisitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'care_contract_id' => CareContract::factory(),
            'scheduled_on' => today()->addDays(fake()->unique()->numberBetween(0, 3650)),
            'status' => CareVisitStatus::Scheduled,
        ];
    }
}
