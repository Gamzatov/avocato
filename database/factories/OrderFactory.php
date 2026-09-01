<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Order;
use App\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = City::query()->first() ?? City::create([
            'name' => 'Переяслав',
            'slug' => 'pereiaslav',
            'phone' => '073 005 40 50',
            'is_active' => true,
        ]);

        return [
            'city_id' => $city->id,
            'status' => OrderStatus::New,
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_address' => fake()->streetAddress(),
            'customer_comment' => fake()->optional()->sentence(),
            'total' => 299,
        ];
    }
}
