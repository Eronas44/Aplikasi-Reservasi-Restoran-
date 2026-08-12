<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\OpeningHour;
use App\Models\Policy;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use App\Models\WaitingList;
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
        'Minuman' => [
            ['Es Teh Tarik', 'Teh tarik khas manis dan creamy.', 35000],
            ['Kopi Tubruk', 'Kopi hitam khas Indonesia.', 30000],
            ['Cappuccino', 'Espresso dengan foam susu.', 42000],
            ['Americano', 'Espresso dengan air panas.', 38000],
            ['Jus Jambu Merah', 'Jus jambu merah segar.', 32000],
            ['Jus Wortel', 'Jus wortel segar kaya vitamin.', 30000],
            ['Lemon Tea', 'Teh dingin dengan perasan lemon.', 35000],
            ['Sparkling Water', 'Air soda mineral.', 25000],
            ['Virgin Mojito', 'Mocktail mint lemon segar.', 45000],
            ['Cokelat Panas', 'Minuman cokelat hangat.', 38000],
        ],
    ];

    /**
     * Menu Resto B - Cabang Boulevard (konsep Asia & Oriental).
     */
    private array $menuRestoB = [
        'Makanan Utama' => [
            ['Chicken Teriyaki Rice', 'Ayam teriyaki khas Jepang dengan nasi hangat.', 98000],
            ['Nasi Goreng Hong Kong', 'Nasi goreng ala Hong Kong dengan udang dan daging asap.', 85000],
            ['Beef Bulgogi Set', 'Daging sapi panggang ala Korea dengan nasi dan kimchi.', 125000],
            ['Mie Goreng Lada Hitam', 'Mie goreng dengan saus lada hitam dan daging sapi.', 78000],
            ['Ramen Tonkotsu Special', 'Mie ramen kuah tonkotsu dengan chashu dan telur.', 110000],
            ['Chicken Katsu Curry', 'Ayam katsu renyah dengan saus kari Jepang.', 88000],
            ['Salt & Pepper Squid', 'Cumi goreng dengan taburan garam dan merica.', 95000],
            ['Nasi Goreng Kimchi', 'Nasi goreng dengan kimchi dan daging cincang.', 82000],
        ],
        'Makanan Pembuka' => [
            ['Gyoza 6 pcs', 'Pangsit goreng isi ayam dan sayuran.', 45000],
            ['Edamame Garlic Butter', 'Kedelai Jepang dengan mentega bawang putih.', 38000],
            ['Chawan Mushi', 'Telur kukus Jepang lembut dengan udang.', 42000],
            ['Chicken Satay Skewers', 'Sate ayam dengan saus kacang.', 55000],
            ['Takoyaki 8 pcs', 'Bola gurita khas Jepang.', 48000],
            ['Tofu Crispy Sambal Matah', 'Tahu goreng krispi dengan sambal matah.', 40000],
            ['Spring Rolls', 'Lumpia goreng isi sayuran.', 35000],
            ['Miso Soup', 'Sup miso klasik dengan tofu dan rumput laut.', 30000],
        ],
        'Makanan Penutup' => [
            ['Matcha Lava Cake', 'Kue lava cokelat dengan saus matcha.', 68000],
            ['Hokkaido Pudding', 'Puding susu khas Hokkaido.', 50000],
            ['Mochi Ice Cream', 'Mochi dengan isi es krim aneka rasa.', 45000],
            ['Red Bean Pancake', 'Pancake kacang merah dengan sirup.', 55000],
            ['Thai Mango Sticky Rice', 'Ketan mangga ala Thailand.', 62000],
            ['Green Tea Ice Cream', 'Es krim teh hijau premium.', 48000],
            ['Sesame Balls', 'Bola wijen goreng isi kacang hijau.', 40000],
            ['Dorayaki Matcha', 'Dorayaki panekuk Jepang isi matcha.', 52000],
        ],
        'Minuman' => [
            ['Iced Matcha Latte', 'Minuman matcha dingin dengan susu.', 45000],
            ['Thai Tea', 'Teh Thailand manis dengan susu.', 40000],
            ['Iced Jasmine Tea', 'Teh melati dingin.', 35000],
            ['Yuzu Lemonade', 'Lemonade yuzu segar.', 48000],
            ['Lychee Iced Tea', 'Teh dingin dengan rasa leci.', 42000],
            ['Kopi Susu Gula Aren', 'Kopi susu dengan gula aren.', 38000],
            ['Melon Soda Float', 'Soda melon dengan es krim vanila.', 45000],
            ['Genmaicha Tea', 'Teh hijau panggang Jepang.', 30000],
        ],
    ];

    /**
     * Menu Resto C - Cabang Central Park (konsep Italian & Pasta).
     */
    private array $menuRestoC = [
        'Makanan Utama' => [
            ['Spaghetti Carbonara', 'Pasta dengan saus krim, parmesan, dan guanciale.', 98000],
            ['Fettuccine Alfredo', 'Fettuccine dengan saus alfredo creamy.', 95000],
            ['Lasagna Bolognese', 'Lasagna berlapis dengan ragù daging sapi.', 110000],
            ['Penne Arrabbiata', 'Penne dengan saus tomat pedas.', 85000],
            ['Risotto Mushroom', 'Risotto jamur dengan keju parmesan.', 115000],
            ['Pappardelle Truffle', 'Pappardelle dengan saus truffle dan daging sapi.', 145000],
            ['Gnocchi Pesto', 'Gnocchi kentang dengan saus pesto basil.', 98000],
            ['Ravioli Ricotta Spinach', 'Ravioli isi ricotta dan bayam dengan saus tomat.', 120000],
        ],
        'Makanan Pembuka' => [
            ['Bruschetta Pomodoro', 'Roti panggang dengan tomat segar dan basil.', 55000],
            ['Caprese Salad', 'Tomat, mozzarella, dan basil segar.', 85000],
            ['Garlic Bread Cheese', 'Roti bawang putih dengan lelehan keju.', 45000],
            ['Calamari Fritti', 'Cumi goreng renyah ala Italia.', 95000],
            ['Burrata Creamy', 'Burrata lembut dengan cherry tomato.', 110000],
            ['Minestrone Soup', 'Sup sayur khas Italia.', 48000],
            ['Antipasti Platter', 'Aneka keju, ham, dan zaitun.', 135000],
            ['Polenta Bites', 'Gorengan polenta dengan saus marinara.', 52000],
        ],
        'Makanan Penutup' => [
            ['Tiramisu Classico', 'Tiramisu klasik dengan mascarpone dan kopi.', 75000],
            ['Panna Cotta Berry', 'Panna cotta dengan saus berry segar.', 65000],
            ['Affogato Vanilla', 'Espresso di atas es krim vanila.', 60000],
            ['Cannoli Siciliani', 'Cannoli isi ricotta manis.', 68000],
            ['Lemon Sorbet', 'Sorbet lemon segar.', 55000],
            ['Amaretti Cookie', 'Kukis almond khas Italia.', 40000],
            ['Gelato Trio', 'Tiga scoop gelato pilihan.', 52000],
            ['Tarte Tatin', 'Pai apel karamel hangat.', 72000],
        ],
        'Minuman' => [
            ['Espresso Doppio', 'Espresso ganda klasik.', 35000],
            ['Cappuccino', 'Cappuccino dengan foam susu.', 42000],
            ['Latte Macchiato', 'Latte dengan foam tebal.', 45000],
            ['Italian Soda', 'Soda rasa buah ala Italia.', 40000],
            ['Limoncello Spritz', 'Minuman segar lemon.', 55000],
            ['Hot Chocolate Cioccolata', 'Cokelat panas kental Italia.', 48000],
            ['Espresso Tonic', 'Espresso dengan soda tonic.', 50000],
            ['Earl Grey Tea', 'Teh Earl Grey klasik.', 35000],
        ],
    ];

    /**
     * Menu Resto D - Cabang Marina (konsep Seafood & Nusantara).
     */
    private array $menuRestoD = [
        'Makanan Utama' => [
            ['Gurami Bakar Pesmol', 'Gurami bakar dengan bumbu pesmol.', 125000],
            ['Nasi Goreng Seafood', 'Nasi goreng dengan aneka seafood segar.', 95000],
            ['Cumi Saus Padang', 'Cumi-cumi dengan saus padang kental.', 110000],
            ['Kepiting Saus Telur Asin', 'Kepiting dengan saus telur asin.', 215000],
            ['Ikan Nila Bumbu Rujak', 'Ikan nila dengan bumbu rujak.', 98000],
            ['Udang Bakar Madu', 'Udang bakar dengan saus madu.', 135000],
            ['Soto Ayam Lamongan', 'Soto ayam khas Lamongan.', 65000],
            ['Rendang Daging Sapi', 'Daging sapi rendang khas Minang.', 115000],
        ],
        'Makanan Pembuka' => [
            ['Perkedel Kentang', 'Perkedel kentang goreng.', 32000],
            ['Tahu Telur', 'Tahu goreng dengan telur dan sambal.', 38000],
            ['Bakwan Jagung', 'Bakwan jagung renyah.', 30000],
            ['Sate Lilit Ayam', 'Sate lilit khas Bali.', 55000],
            ['Otak-otak Bakar', 'Otak-otak ikan bakar dengan bumbu.', 48000],
            ['Rujak Cingur', 'Rujak cingur khas Surabaya.', 42000],
            ['Emping Goreng', 'Keripik emping melinjo.', 25000],
            ['Sate Bandeng', 'Sate daging bandeng khas Banten.', 52000],
        ],
        'Makanan Penutup' => [
            ['Es Teler', 'Es teler dengan alpukat dan kelapa muda.', 42000],
            ['Es Campur', 'Es campur dengan aneka topping.', 40000],
            ['Klepon', 'Kue klepon berisi gula merah.', 35000],
            ['Dadar Gulung', 'Dadar gulung isi kelapa manis.', 32000],
            ['Pisang Goreng Keju', 'Pisang goreng dengan keju dan cokelat.', 38000],
            ['Kolak Pisang', 'Kolak pisang hangat dengan santan.', 35000],
            ['Getuk Lindri', 'Getuk lindri manis lembut.', 30000],
            ['Arem-arem Mini', 'Lontong isi sayuran gurih.', 30000],
        ],
        'Minuman' => [
            ['Es Jeruk Peras', 'Jus jeruk segar.', 25000],
            ['Es Kelapa Muda', 'Es kelapa muda segar.', 30000],
            ['Jus Alpukat Cokelat', 'Jus alpukat dengan sirup cokelat.', 38000],
            ['Wedang Jahe', 'Minuman jahe hangat.', 25000],
            ['Es Teh Manis', 'Es teh manis.', 15000],
            ['Kopi Luwak Premium', 'Kopi luwak asli Indonesia.', 85000],
            ['Es Blewah', 'Es blewah segar.', 25000],
            ['Susu Jahe Madu', 'Susu hangat dengan jahe dan madu.', 35000],
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@reservasi.local',
            'phone_number' => '081111111111',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Staff users (from kelola_staf.php fallback)
        User::factory()->create([
            'name' => 'Front Staff',
            'email' => 'staff@reservasi.local',
            'phone_number' => '082222222222',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::factory()->create([
            'name' => 'Resepsionis A',
            'email' => 'resepsionis@resto.local',
            'phone_number' => '083333333333',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::factory()->create([
            'name' => 'Resepsionis B',
            'email' => 'resepsionis2@resto.local',
            'phone_number' => '084444444444',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::factory()->create([
            'name' => 'Kasir',
            'email' => 'kasir@resto.local',
            'phone_number' => '085555555555',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // Customer users (from fallback demo data)
        $customerUsers = [
            ['Budi Santoso', 'budi.santoso@example.com', '0812-3456-7890'],
            ['Siti Rahma', 'siti.rahma@example.com', '0813-2222-3333'],
            ['Andi Wijaya', 'andi.wijaya@example.com', '0814-5555-6666'],
            ['Rina Kartika', 'rina.kartika@example.com', '0815-7777-8888'],
            ['Bima Pratama', 'bima.pratama@example.com', '0816-9999-0000'],
            ['Dewi Lestari', 'dewi.lestari@example.com', '0817-1111-2222'],
        ];

        $createdCustomers = [];
        foreach ($customerUsers as [$name, $email, $phone]) {
            $createdCustomers[] = User::factory()->create([
                'name' => $name,
                'email' => $email,
                'phone_number' => $phone,
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]);
        }

        User::factory(10)->create(['role' => 'customer']);

        // Restoran (Resto A/B/C/D sesuai dashboard user)
        $restos = [
            ['Resto A - Cabang Utama', 'resto-a', 'Jl. Soekarno Hatta No.113, Lampung', '082965739824', 'resto.a@kafiber.id', 4.8],
            ['Resto B - Cabang Boulevard', 'resto-b', 'Jl. Boulevard Selatan No.45, Lampung', '082965739825', 'resto.b@kafiber.id', 4.6],
            ['Resto C - Cabang Central Park', 'resto-c', 'Jl. ZA Pagar Alam No.88, Bandar Lampung', '082965739826', 'resto.c@kafiber.id', 4.7],
            ['Resto D - Cabang Marina', 'resto-d', 'Jl. Pangeran Antasari No.21, Lampung', '082965739827', 'resto.d@kafiber.id', 4.5],
        ];

        foreach ($restos as $i => [$name, $slug, $address, $phone, $email, $rating]) {
            Restaurant::query()->create([
                'name' => $name,
                'slug' => $slug,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'rating' => $rating,
                'image_url' => null,
                'is_active' => true,
            ]);

            // Jam operasional default per hari (0=Minggu .. 6=Sabtu)
            foreach (range(0, 6) as $day) {
                $isWeekend = $day === 0 || $day === 6;
                OpeningHour::query()->create([
                    'restaurant_id' => $i + 1,
                    'day_of_week' => $day,
                    'open_time' => $isWeekend ? '09:00' : '10:00',
                    'close_time' => $isWeekend ? '22:00' : '23:00',
                    'is_closed' => false,
                ]);
            }

            // Kebijakan deposit & refund default (FR-007, FR-014)
            Policy::query()->create([
                'restaurant_id' => $i + 1,
                'deposit_percent' => 20,
                'deposit_min_amount' => 50000,
                'refund_full_hours' => 24,
                'refund_partial_hours' => 6,
                'refund_partial_percent' => 50,
                'is_active' => true,
            ]);
        }

        // Meja dikaitkan ke setiap cabang restoran (dari denah_meja.php dan kelola_meja.php fallback)
        $tableSeedData = [
            ['T01', 2, 'indoor', 'available'],
            ['T02', 2, 'indoor', 'available'],
            ['T03', 4, 'indoor', 'available'],
            ['T04', 4, 'indoor', 'available'],
            ['T05', 2, 'indoor', 'available'],
            ['T06', 6, 'indoor', 'available'],
            ['T07', 4, 'indoor', 'occupied'],
            ['T08', 6, 'outdoor', 'reserved'],
            ['T09', 2, 'outdoor', 'available'],
            ['T10', 4, 'outdoor', 'available'],
            ['T11', 8, 'vip', 'reserved'],
            ['T12', 8, 'vip', 'available'],
            ['T13', 4, 'smoking', 'available'],
            ['T14', 4, 'smoking', 'available'],
            ['T15', 6, 'outdoor', 'available'],
            ['T16', 6, 'outdoor', 'available'],
        ];

        foreach ($restos as $i => $_) {
            foreach ($tableSeedData as [$tableNumber, $capacity, $locationArea, $status]) {
                RestaurantTable::query()->create([
                    'restaurant_id' => $i + 1,
                    'table_number' => $tableNumber,
                    'capacity' => $capacity,
                    'location_area' => $locationArea,
                    'status' => $status,
                ]);
            }
        }

        // Kategori & menu diambil dari MENU_RESTORAN_D.docx; tiap cabang kini
        // memiliki daftar menu yang BERBEDA sesuai konsepnya masing-masing.
        $categories = [];
        foreach ($this->menuData as $categoryName => $items) {
            $categories[$categoryName] = Category::query()->create([
                'category_name' => $categoryName,
            ]);
        }

        $menuByResto = [$this->menuData, $this->menuRestoB, $this->menuRestoC, $this->menuRestoD];

        foreach ($restos as $i => $_) {
            $restaurantId = $i + 1;
            $restoMenu = $menuByResto[$i] ?? $this->menuData;

            foreach ($restoMenu as $categoryName => $items) {
                $category = $categories[$categoryName];

                foreach ($items as [$itemName, $description, $price]) {
                    Menu::query()->create([
                        'category_id' => $category->category_id,
                        'restaurant_id' => $restaurantId,
                        'item_name' => $itemName,
                        'description' => $description,
                        'price' => $price,
                        'is_available' => true,
                    ]);
                }
            }
        }

        // Create demo reservations from fallback data
        $today = date('Y-m-d');
        $demoReservations = [
            [
                'user_id' => $createdCustomers[0]->user_id, // Budi Santoso
                'table_id' => 12, // T12
                'reservation_date' => $today,
                'reservation_time' => '12:00',
                'number_of_guest' => 4,
                'status' => 'confirmed',
                'booking_code' => 'KB-000001',
            ],
            [
                'user_id' => $createdCustomers[1]->user_id, // Siti Rahma
                'table_id' => 5, // T05
                'reservation_date' => $today,
                'reservation_time' => '12:30',
                'number_of_guest' => 2,
                'status' => 'confirmed',
                'booking_code' => 'KB-000002',
            ],
            [
                'user_id' => $createdCustomers[2]->user_id, // Andi Wijaya
                'table_id' => 8, // T08
                'reservation_date' => $today,
                'reservation_time' => '13:00',
                'number_of_guest' => 6,
                'status' => 'pending',
                'booking_code' => 'KB-000003',
            ],
            [
                'user_id' => $createdCustomers[3]->user_id, // Rina Kartika
                'table_id' => 11, // T11
                'reservation_date' => $today,
                'reservation_time' => '18:00',
                'number_of_guest' => 8,
                'status' => 'confirmed',
                'booking_code' => 'KB-000005',
            ],
            [
                'user_id' => $createdCustomers[4]->user_id, // Bima Pratama
                'table_id' => 10, // T10
                'reservation_date' => $today,
                'reservation_time' => '19:00',
                'number_of_guest' => 5,
                'status' => 'confirmed',
                'booking_code' => 'KB-000007',
            ],
            [
                'user_id' => $createdCustomers[5]->user_id, // Dewi Lestari
                'table_id' => 7, // T07
                'reservation_date' => date('Y-m-d', strtotime($today . ' +1 day')),
                'reservation_time' => '13:00',
                'number_of_guest' => 3,
                'status' => 'cancelled',
                'booking_code' => 'KB-0005',
            ],
        ];

        foreach ($demoReservations as $resData) {
            Reservation::query()->create(array_merge($resData, [
                'total_price' => 0,
                'deposit_amount' => 0,
            ]));
        }

        // Additional random reservations
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

        // Seed waiting list data (from walkin.php fallback)
        $waitingListData = [
            ['Bayu Saputra', null, 4, 'outdoor', $today . ' 10:15:00'],
            ['Indah Permata', null, 6, 'vip', $today . ' 10:30:00'],
            ['Fajar Nugroho', null, 2, 'indoor', $today . ' 10:45:00'],
        ];

        foreach ($waitingListData as [$name, $phone, $guests, $area, $createdAt]) {
            WaitingList::query()->create([
                'restaurant_id' => 1,
                'name' => $name,
                'phone' => $phone,
                'number_of_guest' => $guests,
                'area' => $area,
                'status' => 'waiting',
                'created_at' => $createdAt,
            ]);
        }
    }
}