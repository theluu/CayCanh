<?php

namespace Database\Factories;

use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectType>
 */
class ProjectTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Biệt thự', 'Văn phòng', 'Khu đô thị', 'Resort', 'Trường học', 'Nhà xưởng']).' '.fake()->numberBetween(1, 999),
            'sort' => 0,
        ];
    }
}
