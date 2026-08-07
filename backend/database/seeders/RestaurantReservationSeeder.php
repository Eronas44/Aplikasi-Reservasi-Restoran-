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
     * Data menu diambil dari MENU_RESTORAN_D.docx.
     *
     * Catatan: pada dokumen aslinya, kategori "Makanan Penutup" berisi
     * konten yang identik dengan "Makanan Pembuka", dan kategori
     * "Minuman" berisi item dessert (bukan minuman). Data di bawah ini
     * disalin apa adanya sesuai dokumen sumber.
     */
    private array $menuData = [
        'Makanan Utama' => [
            ['Teppanyaki Wagyu Steak', 'Daging wagyu premium dimasak langsung di atas grill teppanyaki dengan sayuran segar.', 295000],
            ['Australian Sirloin Steak', 'Steak sirloin Australia dengan saus black pepper atau mushroom.', 225000],
            ['Grilled Atlantic Salmon', 'Salmon panggang dengan saus lemon butter dan asparagus.', 210000],
            ['Honey Garlic Beef Ribs', 'Iga sapi panggang dengan saus madu dan bawang putih.', 235000],
            ['Teppanyaki Chicken', 'Ayam fillet dimasak di atas grill teppanyaki dengan saus teriyaki.', 145000],
            ['Seafood Teppanyaki', 'Kombinasi udang, cumi, dan kerang dimasak langsung oleh koki.', 195000],
            ['Lobster Thermidor', 'Lobster panggang dengan saus krim keju khas Prancis.', 385000],
            ['Butter Garlic Prawn', 'Udang besar dengan mentega dan bawang putih.', 185000],
            ['Seafood Marinara Pasta', 'Pasta dengan saus tomat dan aneka seafood segar.', 155000],
            ['Truffle Mushroom Pasta', 'Pasta creamy dengan jamur dan aroma minyak truffle.', 165000],
            ['Chicken Cordon Bleu', 'Ayam isi smoked beef dan keju dengan kentang goreng.', 145000],
            ['Lamb Chop Rosemary', 'Daging domba panggang dengan saus rosemary.', 245000],
            ['Beef Teriyaki Bowl', 'Irisan daging sapi dengan saus teriyaki dan nasi Jepang.', 125000],
            ['Grilled Tuna Steak', 'Tuna segar dipanggang dengan saus citrus.', 185000],
            ['Chicken Parmigiana', 'Ayam crispy dengan mozzarella dan spaghetti.', 145000],
            ['Surf & Turf Platter', 'Kombinasi steak sapi dan seafood premium.', 345000],
            ['Fish & Chips Premium', 'Fillet ikan goreng renyah dengan kentang dan saus tartar.', 135000],
            ['Japanese Curry Katsu', 'Ayam katsu dengan kari Jepang dan nasi.', 120000],
            ['Seafood Fried Rice', 'Nasi goreng premium dengan udang dan cumi.', 110000],
            ['Signature Waterfront Platter', 'Kombinasi steak, salmon, udang, dan sayuran panggang.', 425000],
        ],
        'Makanan Pembuka' => [
            ['Caesar Salad', 'Selada romaine, parmesan, dan saus Caesar.', 68000],
            ['Smoked Salmon Salad', 'Salad segar dengan irisan salmon asap.', 98000],
            ['Burrata Tomato Salad', 'Keju burrata lembut dengan tomat cherry dan basil.', 110000],
            ['Garlic Bread', 'Roti panggang dengan mentega bawang putih.', 38000],
            ['Bruschetta', 'Roti panggang dengan tomat segar dan olive oil.', 58000],
            ['Truffle Fries', 'Kentang goreng dengan minyak truffle dan parmesan.', 65000],
            ['Cheese Fries', 'Kentang goreng dengan saus keju leleh.', 58000],
            ['Shrimp Tempura', 'Udang goreng tepung khas Jepang.', 95000],
            ['Crispy Calamari', 'Cumi goreng renyah dengan saus tartar.', 88000],
            ['Chicken Wings BBQ', 'Sayap ayam dengan saus BBQ.', 78000],
            ['Korean Chicken Wings', 'Sayap ayam dengan saus Korea pedas manis.', 82000],
            ['Mozzarella Sticks', 'Keju mozzarella goreng dengan saus marinara.', 68000],
            ['Onion Rings', 'Bawang bombai goreng tepung renyah.', 52000],
            ['Cream Mushroom Soup', 'Sup krim jamur dengan roti panggang.', 58000],
            ['Seafood Chowder', 'Sup krim seafood dengan kentang dan jagung.', 82000],
            ['Pumpkin Soup', 'Sup labu kuning bertekstur lembut.', 55000],
            ['Nachos Supreme', 'Nachos dengan daging cincang dan keju.', 88000],
            ['Mini Beef Sliders', 'Mini burger daging sapi premium.', 98000],
            ['Sushi Platter', 'Aneka sushi segar pilihan chef.', 145000],
            ['Charcuterie Board', 'Aneka keju, smoked beef, buah, dan crackers.', 195000],
        ],
        'Makanan Penutup' => [
            ['Caesar Salad', 'Selada romaine, parmesan, dan saus Caesar.', 68000],
            ['Smoked Salmon Salad', 'Salad segar dengan irisan salmon asap.', 98000],
            ['Burrata Tomato Salad', 'Keju burrata lembut dengan tomat cherry dan basil.', 110000],
            ['Garlic Bread', 'Roti panggang dengan mentega bawang putih.', 38000],
            ['Bruschetta', 'Roti panggang dengan tomat segar dan olive oil.', 58000],
            ['Truffle Fries', 'Kentang goreng dengan minyak truffle dan parmesan.', 65000],
            ['Cheese Fries', 'Kentang goreng dengan saus keju leleh.', 58000],
            ['Shrimp Tempura', 'Udang goreng tepung khas Jepang.', 95000],
            ['Crispy Calamari', 'Cumi goreng renyah dengan saus tartar.', 88000],
            ['Chicken Wings BBQ', 'Sayap ayam dengan saus BBQ.', 78000],
            ['Korean Chicken Wings', 'Sayap ayam dengan saus Korea pedas manis.', 82000],
            ['Mozzarella Sticks', 'Keju mozzarella goreng dengan saus marinara.', 68000],
            ['Onion Rings', 'Bawang bombai goreng tepung renyah.', 52000],
            ['Cream Mushroom Soup', 'Sup krim jamur dengan roti panggang.', 58000],
            ['Seafood Chowder', 'Sup krim seafood dengan kentang dan jagung.', 82000],
            ['Pumpkin Soup', 'Sup labu kuning bertekstur lembut.', 55000],
            ['Nachos Supreme', 'Nachos dengan daging cincang dan keju.', 88000],
            ['Mini Beef Sliders', 'Mini burger daging sapi premium.', 98000],
            ['Sushi Platter', 'Aneka sushi segar pilihan chef.', 145000],
            ['Charcuterie Board', 'Aneka keju, smoked beef, buah, dan crackers.', 195000],
        ],
        'Minuman' => [
            ['Chocolate Lava Cake', 'Kue cokelat hangat dengan lelehan cokelat.', 68000],
            ['New York Cheesecake', 'Cheesecake klasik dengan topping buah segar.', 72000],
            ['Tiramisu', 'Dessert Italia dengan mascarpone dan kopi.', 75000],
            ['Crème Brûlée', 'Custard vanila dengan lapisan gula karamel.', 68000],
            ['Red Velvet Cake', 'Kue red velvet dengan cream cheese frosting.', 65000],
            ['Brownies with Ice Cream', 'Brownies cokelat hangat dengan es krim vanila.', 62000],
            ['Oreo Cheesecake', 'Cheesecake dengan remahan biskuit Oreo.', 68000],
            ['Matcha Cheesecake', 'Cheesecake rasa matcha premium.', 70000],
            ['Vanilla Panna Cotta', 'Puding krim Italia dengan saus berry.', 60000],
            ['Chocolate Mousse', 'Mousse cokelat lembut.', 58000],
            ['Belgian Waffle', 'Waffle hangat dengan madu dan buah segar.', 68000],
            ['Pancake Berry Delight', 'Pancake lembut dengan saus berry.', 65000],
            ['Banana Split', 'Pisang dengan tiga scoop es krim dan saus cokelat.', 68000],
            ['Fruit Platter', 'Aneka buah segar pilihan.', 58000],
            ['Mango Sticky Rice', 'Ketan manis dengan mangga matang.', 65000],
            ['Vanilla Gelato', 'Gelato vanila premium.', 52000],
            ['Chocolate Gelato', 'Gelato cokelat premium.', 52000],
            ['Strawberry Gelato', 'Gelato stroberi premium.', 52000],
            ['Affogato', 'Espresso disajikan di atas es krim vanila.', 60000],
            ['Apple Crumble', 'Pai apel hangat dengan es krim vanila.', 72000],
        ],
    ];

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

        // Kategori & menu diambil dari MENU_RESTORAN_D.docx
        foreach ($this->menuData as $categoryName => $items) {
            $category = Category::query()->create([
                'category_name' => $categoryName,
            ]);

            foreach ($items as [$itemName, $description, $price]) {
                Menu::query()->create([
                    'category_id' => $category->category_id,
                    'item_name' => $itemName,
                    'description' => $description,
                    'price' => $price,
                    'is_available' => true,
                ]);
            }
        }

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