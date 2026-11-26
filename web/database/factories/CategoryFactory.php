<?php

namespace Database\Factories;

use App\Enums\ProductKind;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'scope' => ProductKind::Standard,
            'name' => fake()->randomElement(['Cây nội thất', 'Cây văn phòng', 'Cây công trình', 'Chậu cảnh', 'Vật tư', 'Cây phong thủy', 'Cây để bàn', 'Cây ngoại thất']).' '.fake()->numberBetween(1, 999),
            'sort' => 0,
            'is_visible' => true,
        ];
    }

    public function art(): static
    {
        return $this->state(['scope' => ProductKind::Art]);
    }
}
