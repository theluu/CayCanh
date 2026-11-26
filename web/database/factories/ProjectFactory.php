<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_type_id' => ProjectType::factory(),
            'name' => 'Công trình '.fake()->unique()->words(3, true),
            'client' => fake()->company(),
            'location' => fake()->city(),
            'year' => fake()->numberBetween(2018, 2026),
            'scale' => fake()->numberBetween(500, 20000).' m²',
            'summary' => fake()->sentence(15),
            'body' => '<p>'.fake()->paragraphs(3, true).'</p>',
            'is_featured' => false,
            'is_published' => true,
        ];
    }
}
