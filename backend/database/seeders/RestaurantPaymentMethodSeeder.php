<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantPaymentMethod;
use Illuminate\Database\Seeder;

class RestaurantPaymentMethodSeeder extends Seeder
{
    /**
     * Seed konfigurasi merchant pembayaran per cabang (simulasi, tanpa gateway).
     *
     * Idempoten: memakai updateOrCreate agar aman dijalankan berulang
     * pada database yang sudah ada (tidak membuat baris duplikat).
     */
    public function run(): void
    {
        $restaurants = Restaurant::query()->orderBy('restaurant_id')->get();

        foreach ($restaurants as $restaurant) {
            $configs = [
                [
                    'method' => 'bank_transfer',
                    'label' => 'Transfer Bank BCA',
                    'account_name' => 'Kafiber ' . $restaurant->name,
                    'account_number' => '8820' . str_pad((string) $restaurant->restaurant_id, 3, '0', STR_PAD_LEFT) . '12345678',
                    'phone_number' => null,
                ],
                [
                    'method' => 'ewallet',
                    'label' => 'E-Wallet (OVO / GoPay / DANA)',
                    'account_name' => 'Kafiber ' . $restaurant->name,
                    'account_number' => null,
                    'phone_number' => $restaurant->phone,
                ],
                [
                    'method' => 'qris',
                    'label' => 'QRIS',
                    'account_name' => 'Kafiber ' . $restaurant->name,
                    'account_number' => null,
                    'phone_number' => null,
                ],
            ];

            foreach ($configs as $config) {
                RestaurantPaymentMethod::query()->updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->restaurant_id,
                        'method' => $config['method'],
                    ],
                    [
                        'label' => $config['label'],
                        'account_name' => $config['account_name'],
                        'account_number' => $config['account_number'],
                        'phone_number' => $config['phone_number'],
                        'qris_image' => null,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
