<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServicePackage>
 */
class ServicePackageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'name' => 'Gói '.fake()->randomElement(['Cơ bản', 'Tiêu chuẩn', 'Cao cấp']),
            'price_from' => fake()->numberBetween(10, 100) * 100000,
            'unit' => '/tháng',
            'features' => ['Tưới cây định kỳ', 'Bón phân', 'Thay cây miễn phí'],
            'is_highlighted' => false,
            'sort' => 0,
        ];
    }
}
