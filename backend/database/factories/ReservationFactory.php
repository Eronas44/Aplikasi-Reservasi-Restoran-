<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled', 'no_show']);

        return [
            'user_id' => User::factory()->state(['role' => 'customer']),
            'table_id' => RestaurantTable::factory(),
            'booking_code' => 'BOOK-' . Str::upper(Str::random(8)),
            'reservation_date' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
            'reservation_time' => fake()->randomElement(['11:00:00', '12:00:00', '13:00:00', '18:00:00', '19:00:00', '20:00:00']),
            'number_of_guest' => fake()->numberBetween(1, 10),
            'status' => $status,
            'total_price' => fake()->randomFloat(2, 0, 1000000),
            'deposit_amount' => fake()->randomFloat(2, 0, 500000),
            'payment_status' => fake()->randomElement(['unpaid', 'partial', 'paid']),
            'staff_id' => User::factory()->state(['role' => 'staff']),
            'special_request' => fake()->boolean(40) ? fake()->sentence() : null,
        ];
    }
}
