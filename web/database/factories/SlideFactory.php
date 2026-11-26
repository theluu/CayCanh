<?php

namespace Database\Factories;

use App\Models\Slide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slide>
 */
class SlideFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'subtitle' => fake()->sentence(10),
            'button_text' => 'Nhận báo giá',
            'button_url' => '/bao-gia',
            'sort' => 0,
            'is_active' => true,
        ];
    }
}
