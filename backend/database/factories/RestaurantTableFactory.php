<?php

namespace Database\Factories;

use App\Models\RestaurantTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RestaurantTable>
 */
class RestaurantTableFactory extends Factory
{
    protected $model = RestaurantTable::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => fake()->numberBetween(1, 4),
            'table_number' => 'T-' . fake()->unique()->numberBetween(1, 80),
            'capacity' => fake()->randomElement([2, 4, 6, 8]),
            'location_area' => fake()->randomElement(['indoor', 'outdoor', 'smoking', 'vip']),
            'status' => fake()->randomElement(['available', 'reserved', 'occupied', 'maintenance']),
        ];
    }
}
