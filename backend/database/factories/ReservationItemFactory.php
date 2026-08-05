<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReservationItem>
 */
class ReservationItemFactory extends Factory
{
    protected $model = ReservationItem::class;

    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'menu_id' => Menu::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'subtotal_price' => fake()->randomFloat(2, 10000, 500000),
        ];
    }
}
