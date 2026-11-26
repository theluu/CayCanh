<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => Order::generateCode(),
            'customer_name' => fake()->name(),
            'phone' => '09'.fake()->numerify('########'),
            'email' => fake()->safeEmail(),
            'province' => 'TP. Hồ Chí Minh',
            'address' => fake()->streetAddress(),
            'payment_method' => PaymentMethod::Cod,
            'status' => OrderStatus::New,
            'subtotal' => fake()->numberBetween(1, 50) * 100000,
        ];
    }
}
