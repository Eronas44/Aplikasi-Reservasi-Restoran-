<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RestaurantReservationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@reservasi.local',
            'phone_number' => '081111111111',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Front Staff',
            'email' => 'staff@reservasi.local',
            'phone_number' => '082222222222',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::factory(10)->create(['role' => 'customer']);
        RestaurantTable::factory(20)->create();

        Category::factory(5)->create()->each(function (Category $category): void {
            Menu::factory(6)->create([
                'category_id' => $category->category_id,
            ]);
        });

        Reservation::factory(20)->create()->each(function (Reservation $reservation): void {
            $menus = Menu::query()->inRandomOrder()->take(random_int(1, 4))->get();
            $total = 0;

            foreach ($menus as $menu) {
                $quantity = random_int(1, 3);
                $subtotal = (float) $menu->price * $quantity;
                $total += $subtotal;

                ReservationItem::query()->create([
                    'reservation_id' => $reservation->reservation_id,
                    'menu_id' => $menu->menu_id,
                    'quantity' => $quantity,
                    'subtotal_price' => $subtotal,
                ]);
            }

            $reservation->update([
                'total_price' => $total,
                'deposit_amount' => round($total * 0.2, 2),
            ]);
        });
    }
}
