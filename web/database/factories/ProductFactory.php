<?php

namespace Database\Factories;

use App\Enums\ArtAvailability;
use App\Enums\ProductKind;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kind' => ProductKind::Standard,
            'name' => fake()->randomElement(['Cây Kim Tiền', 'Cây Lưỡi Hổ', 'Cây Trầu Bà', 'Cây Phát Tài', 'Cây Bàng Singapore', 'Chậu xi măng']).' '.fake()->numberBetween(1, 9999),
            'price' => fake()->numberBetween(10, 500) * 10000,
            'sale_price' => null,
            'in_stock' => true,
            'summary' => fake()->sentence(12),
            'description' => '<p>'.fake()->paragraph(4).'</p>',
            'specs' => ['Chiều cao' => fake()->numberBetween(30, 200).' cm'],
            'is_published' => true,
            'is_featured' => false,
        ];
    }

    public function art(): static
    {
        return $this->state([
            'kind' => ProductKind::Art,
            'name' => fake()->randomElement(['Tùng La Hán', 'Sanh Cổ', 'Mai Chiếu Thủy', 'Linh Sam', 'Duối Cổ', 'Khế Cổ']).' '.fake()->numberBetween(1, 9999),
            'show_price' => false,
            'availability' => ArtAvailability::Available,
            'specs' => ['Dáng thế' => 'Trực', 'Chiều cao' => '120 cm', 'Hoành tán' => '90 cm', 'Tuổi cây' => '30 năm'],
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(['is_published' => false]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
