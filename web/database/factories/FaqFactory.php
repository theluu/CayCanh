<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => rtrim(fake()->sentence(8), '.').'?',
            'answer' => fake()->paragraph(3),
            'sort' => 0,
            'is_published' => true,
        ];
    }
}
