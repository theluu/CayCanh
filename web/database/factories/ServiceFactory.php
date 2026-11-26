<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Dịch vụ '.fake()->unique()->words(2, true),
            'summary' => fake()->sentence(15),
            'body' => '<p>'.fake()->paragraphs(3, true).'</p>',
            'sort' => fake()->numberBetween(0, 10),
            'is_published' => true,
        ];
    }
}
