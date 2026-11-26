<?php

namespace Database\Factories;

use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostCategory>
 */
class PostCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Kiến thức chăm cây', 'Tin tức', 'Phong thủy', 'Cảnh quan']).' '.fake()->numberBetween(1, 999),
            'sort' => 0,
        ];
    }
}
