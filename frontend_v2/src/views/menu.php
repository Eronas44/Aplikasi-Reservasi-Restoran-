<?php
// menu.php — Halaman Daftar Menu Otomatis Sesuai Restoran yang Dipilih
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Tangkap restoran yang dipilih. Jika belum ada, kembalikan ke default A.
$selected_resto = isset($_GET['resto']) ? strtoupper($_GET['resto']) : 'A';
if (!in_array($selected_resto, ['A', 'B'])) {
    $selected_resto = 'A';
}

// Mapping nama restoran lengkap
$daftar_resto = [
    'A' => 'RESTO A - Cabang Utama',
    'B' => 'RESTO B - Cabang Boulevard'
];

$restaurantName = $daftar_resto[$selected_resto];

// Database Menu Berdasarkan Resto A dan Resto B
$menu_restoran = [
    'A' => [
        'MAKANAN UTAMA' => [
            ['nama' => 'Grilled Sirloin Steak', 'harga' => 'Rp 175.000'],
            ['nama' => 'Wagyu Ribeye Steak', 'harga' => 'Rp 265.000'],
            ['nama' => 'Honey BBQ Beef Ribs', 'harga' => 'Rp 195.000'],
            ['nama' => 'Salmon Lemon Butter', 'harga' => 'Rp 185.000'],
            ['nama' => 'Chicken Parmigiana', 'harga' => 'Rp 125.000'],
            ['nama' => 'Chicken Cordon Bleu', 'harga' => 'Rp 135.000'],
            ['nama' => 'Grilled Chicken Rosemary', 'harga' => 'Rp 120.000'],
            ['nama' => 'Fish & Chips', 'harga' => 'Rp 115.000'],
            ['nama' => 'Beef Teriyaki Rice Bowl', 'harga' => 'Rp 105.000'],
            ['nama' => 'Chicken Katsu Curry', 'harga' => 'Rp 99.000'],
            ['nama' => 'Seafood Aglio Olio', 'harga' => 'Rp 135.000'],
            ['nama' => 'Spaghetti Carbonara', 'harga' => 'Rp 115.000'],
            ['nama' => 'Spaghetti Bolognese', 'harga' => 'Rp 110.000'],
            ['nama' => 'Beef Black Pepper', 'harga' => 'Rp 145.000'],
            ['nama' => 'Seafood Platter', 'harga' => 'Rp 295.000'],
        ],
        'MAKANAN PEMBUKA' => [
            ['nama' => 'Caesar Salad', 'harga' => 'Rp 58.000'],
            ['nama' => 'Garden Salad', 'harga' => 'Rp 52.000'],
            ['nama' => 'Greek Salad', 'harga' => 'Rp 68.000'],
            ['nama' => 'Cream Mushroom Soup', 'harga' => 'Rp 52.000'],
            ['nama' => 'Pumpkin Soup', 'harga' => 'Rp 55.000'],
            ['nama' => 'Chicken Corn Soup', 'harga' => 'Rp 48.000'],
            ['nama' => 'Garlic Bread', 'harga' => 'Rp 32.000'],
            ['nama' => 'Cheese Fries', 'harga' => 'Rp 45.000'],
            ['nama' => 'Truffle Fries', 'harga' => 'Rp 58.000'],
            ['nama' => 'Onion Rings', 'harga' => 'Rp 42.000'],
            ['nama' => 'Mozzarella Sticks', 'harga' => 'Rp 58.000'],
            ['nama' => 'Chicken Wings BBQ', 'harga' => 'Rp 68.000'],
            ['nama' => 'Crispy Calamari', 'harga' => 'Rp 75.000'],
            ['nama' => 'Nachos Supreme', 'harga' => 'Rp 75.000'],
            ['nama' => 'Mini Beef Sliders', 'harga' => 'Rp 88.000'],
        ],
        'MAKANAN PENUTUP' => [
            ['nama' => 'Chocolate Lava Cake', 'harga' => 'Rp 58.000'],
            ['nama' => 'New York Cheesecake', 'harga' => 'Rp 60.000'],
            ['nama' => 'Tiramisu', 'harga' => 'Rp 62.000'],
            ['nama' => 'Brownies with Ice Cream', 'harga' => 'Rp 55.000'],
            ['nama' => 'Banana Split', 'harga' => 'Rp 58.000'],
            ['nama' => 'Panna Cotta Berry', 'harga' => 'Rp 52.000'],
            ['nama' => 'Vanilla Gelato', 'harga' => 'Rp 45.000'],
            ['nama' => 'Matcha Gelato', 'harga' => 'Rp 48.000'],
            ['nama' => 'Mango Sticky Rice', 'harga' => 'Rp 58.000'],
            ['nama' => 'Crème Brûlée', 'harga' => 'Rp 60.000'],
            ['nama' => 'Belgian Waffle', 'harga' => 'Rp 58.000'],
            ['nama' => 'Pancake Maple Syrup', 'harga' => 'Rp 52.000'],
            ['nama' => 'Strawberry Cheesecake', 'harga' => 'Rp 60.000'],
            ['nama' => 'Red Velvet Cake', 'harga' => 'Rp 58.000'],
            ['nama' => 'Chocolate Mousse', 'harga' => 'Rp 52.000'],
        ],
        'MINUMAN' => [
            ['nama' => 'Signature Tropical Sunrise', 'harga' => 'Rp 48.000'],
            ['nama' => 'Virgin Mojito', 'harga' => 'Rp 48.000'],
            ['nama' => 'Blue Ocean Mocktail', 'harga' => 'Rp 50.000'],
            ['nama' => 'Lychee Sparkling', 'harga' => 'Rp 48.000'],
            ['nama' => 'Mango Passion', 'harga' => 'Rp 50.000'],
            ['nama' => 'Strawberry Lemonade', 'harga' => 'Rp 48.000'],
            ['nama' => 'Fresh Orange Juice', 'harga' => 'Rp 40.000'],
            ['nama' => 'Fresh Mango Juice', 'harga' => 'Rp 42.000'],
            ['nama' => 'Fresh Watermelon Juice', 'harga' => 'Rp 38.000'],
            ['nama' => 'Fresh Avocado Juice', 'harga' => 'Rp 45.000'],
            ['nama' => 'Cappuccino', 'harga' => 'Rp 45.000'],
            ['nama' => 'Caramel Latte', 'harga' => 'Rp 50.000'],
            ['nama' => 'Matcha Latte', 'harga' => 'Rp 48.000'],
            ['nama' => 'Chocolate Milkshake', 'harga' => 'Rp 52.000'],
            ['nama' => 'Lychee Iced Tea', 'harga' => 'Rp 38.000'],
        ]
    ],
    'B' => [
        'MAKANAN UTAMA' => [
            ['nama' => 'Australian Wagyu Ribeye Steak', 'harga' => 'Rp 285.000'],
            ['nama' => 'Black Angus Sirloin Steak', 'harga' => 'Rp 225.000'],
            ['nama' => 'Honey BBQ Beef Ribs', 'harga' => 'Rp 210.000'],
            ['nama' => 'Grilled Norwegian Salmon', 'harga' => 'Rp 195.000'],
            ['nama' => 'Herb Grilled Chicken', 'harga' => 'Rp 125.000'],
            ['nama' => 'Lamb Chop Rosemary', 'harga' => 'Rp 235.000'],
            ['nama' => 'Chicken Parmigiana', 'harga' => 'Rp 135.000'],
            ['nama' => 'Seafood Paella', 'harga' => 'Rp 180.000'],
            ['nama' => 'Seafood Aglio Olio', 'harga' => 'Rp 145.000'],
            ['nama' => 'Truffle Mushroom Pasta', 'harga' => 'Rp 155.000'],
            ['nama' => 'Seafood Marinara Pasta', 'harga' => 'Rp 150.000'],
            ['nama' => 'Butter Garlic Prawn', 'harga' => 'Rp 175.000'],
            ['nama' => 'Grilled Tuna Steak', 'harga' => 'Rp 165.000'],
            ['nama' => 'Roasted Duck with Orange Sauce', 'harga' => 'Rp 195.000'],
            ['nama' => 'Premium Beef Burger', 'harga' => 'Rp 130.000'],
        ],
        'MAKANAN PEMBUKA' => [
            ['nama' => 'Caesar Salad', 'harga' => 'Rp 65.000'],
            ['nama' => 'Smoked Salmon Salad', 'harga' => 'Rp 95.000'],
            ['nama' => 'Burrata Tomato Salad', 'harga' => 'Rp 110.000'],
            ['nama' => 'Garlic Bread', 'harga' => 'Rp 35.000'],
            ['nama' => 'Truffle French Fries', 'harga' => 'Rp 60.000'],
            ['nama' => 'Chicken Wings Korean', 'harga' => 'Rp 78.000'],
            ['nama' => 'Spring Rolls', 'harga' => 'Rp 55.000'],
            ['nama' => 'Mini Beef Sliders', 'harga' => 'Rp 95.000'],
            ['nama' => 'Chili Lime Prawn Skewers', 'harga' => 'Rp 68.000'],
            ['nama' => 'Arancini Balls with Truffle Aioli', 'harga' => 'Rp 62.000'],
            ['nama' => 'Baked Spinach Artichoke Dip', 'harga' => 'Rp 75.000'],
            ['nama' => 'Quesadilla Con Pollo', 'harga' => 'Rp 65.000'],
            ['nama' => 'Crispy Enoki Mushroom Fries', 'harga' => 'Rp 45.000'],
            ['nama' => 'Crispy Zucchini Fries', 'harga' => 'Rp 48.000'],
            ['nama' => 'Loaded Potato Skins', 'harga' => 'Rp 58.000'],
        ],
        'MAKANAN PENUTUP' => [
            ['nama' => 'Signature Molten Chocolate Lava Cake', 'harga' => 'Rp 55.000'],
            ['nama' => 'Classic Apple Crumble', 'harga' => 'Rp 48.000'],
            ['nama' => 'Sticky Toffee Pudding', 'harga' => 'Rp 52.000'],
            ['nama' => 'Banoffee Pie Jar', 'harga' => 'Rp 48.000'],
            ['nama' => 'Warm Chocolate Brownie Skillet', 'harga' => 'Rp 58.000'],
            ['nama' => 'Classic Crème Brûlée', 'harga' => 'Rp 48.000'],
            ['nama' => 'Tiramisu in Glass', 'harga' => 'Rp 55.000'],
            ['nama' => 'Panna Cotta with Berry Compote', 'harga' => 'Rp 48.000'],
            ['nama' => 'Lemon Meringue Tart', 'harga' => 'Rp 45.000'],
            ['nama' => 'Assorted Gelato & Sorbet Platter', 'harga' => 'Rp 55.000'],
            ['nama' => 'Affogato al Caffè', 'harga' => 'Rp 42.000'],
            ['nama' => 'Matcha Green Tea Crepe Cake', 'harga' => 'Rp 55.000'],
            ['nama' => 'Chocolate Mousse Cake', 'harga' => 'Rp 50.000'],
            ['nama' => 'Carrot Cake with Cream Cheese Frosting', 'harga' => 'Rp 48.000'],
            ['nama' => 'Strawberry Shortcake Jar', 'harga' => 'Rp 50.000'],
        ],
        'MINUMAN' => [
            ['nama' => 'Skyline Sunset Mocktail', 'harga' => 'Rp 55.000'],
            ['nama' => 'Rooftop Blue Lagoon', 'harga' => 'Rp 55.000'],
            ['nama' => 'Virgin Rooftop Mojito', 'harga' => 'Rp 52.000'],
            ['nama' => 'Tropical Sunrise Punch', 'harga' => 'Rp 55.000'],
            ['nama' => 'Berry Sparkling Refresher', 'harga' => 'Rp 58.000'],
            ['nama' => 'Passion Fruit Mint Fizz', 'harga' => 'Rp 58.000'],
            ['nama' => 'Lychee Breeze Mocktail', 'harga' => 'Rp 55.000'],
            ['nama' => 'Green Apple Splash', 'harga' => 'Rp 55.000'],
            ['nama' => 'Mango Sunset Cooler', 'harga' => 'Rp 52.000'],
            ['nama' => 'Matcha Latte Premium', 'harga' => 'Rp 52.000'],
            ['nama' => 'Fresh Orange Juice', 'harga' => 'Rp 45.000'],
            ['nama' => 'Rooftop Midnight Blue (Cocktail)', 'harga' => 'Rp 85.000'],
            ['nama' => 'Classic Espresso Martini (Cocktail)', 'harga' => 'Rp 90.000'],
            ['nama' => 'Café Latte', 'harga' => 'Rp 48.000'],
            ['nama' => 'Classic Americano', 'harga' => 'Rp 42.000'],
        ]
    ]
];

$menu_kategori = $menu_restoran[$selected_resto];

include LAYOUTS_PATH . '/header.php';
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar Menu Navigasi Kiri -->
        <div class="lg:col-span-1 space-y-3">
            <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-4 shadow-sm space-y-2">
                
                <!-- Pilih Restoran -->
                <a href="<?= route('dashboard') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#5e392e] hover:bg-[#efebe4] font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Pilih Restoran
                </a>

                <!-- Reservasi Restoran -->
                <a href="<?= route('reservasi') ?>&resto=<?= $selected_resto ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#5e392e] hover:bg-[#efebe4] font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Reservasi Restoran
                </a>

                <!-- Menu (Aktif) -->
                <a href="<?= route('menu') ?>&resto=<?= $selected_resto ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#5e392e] text-white font-medium text-sm transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Menu
                </a>

            </div>
        </div>

        <!-- Main Content: Daftar Menu Otomatis Resto Terpilih -->
        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm backdrop-blur space-y-10">
                
                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="eyebrow">Katalog Pilihan</span>
                    <h1 class="font-display text-2xl md:text-3xl font-bold text-[#201913] mt-1">Daftar Menu & Harga <?= e($restaurantName) ?></h1>
                    <p class="text-xs text-stone-500 mt-1">Menampilkan menu resmi secara otomatis untuk restoran yang Anda pilih.</p>
                </div>

                <!-- Perulangan Kategori Menu -->
                <?php foreach ($menu_kategori as $kategori => $items): ?>
                    <div class="space-y-4">
                        <div class="inline-block bg-[#f4cccc] text-[#5e392e] px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border border-[#e8b4b4]">
                            <?= e($kategori) ?>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <?php foreach ($items as $menu): ?>
                                <div class="bg-[#f9f6f0] border border-[#eadfd4] rounded-2xl p-4 flex flex-col justify-between shadow-sm hover:shadow transition">
                                    
                                    <div class="h-32 w-full bg-stone-300 rounded-xl mb-3 overflow-hidden flex items-center justify-center text-stone-500 text-xs font-medium border border-[#eadfd4]">
                                        FOTO <?= $kategori === 'MINUMAN' ? 'MINUMAN' : 'MAKANAN' ?>
                                    </div>

                                    <div class="space-y-1 mb-4">
                                        <h3 class="font-bold text-sm text-[#201913]"><?= e($menu['nama']) ?></h3>
                                        <p class="text-xs font-bold text-[#8a5d49]"><?= e($menu['harga']) ?></p>
                                    </div>

                                    <!-- Dua Tombol: Lihat Detail & Pesan -->
                                    <div class="flex items-center gap-2 pt-2 border-t border-[#eadfd4]">
                                        <a href="#" class="w-1/2 bg-[#efebe4] hover:bg-[#e2dcd2] text-[#5e392e] text-[11px] font-bold py-2 px-2 rounded-lg transition text-center border border-[#eadfd4] shadow-sm">
                                            Lihat Detail
                                        </a>
                                        <a href="<?= route('reservasi') ?>&resto=<?= $selected_resto ?>" class="w-1/2 bg-[#5e392e] hover:bg-[#4a2c24] text-white text-[11px] font-bold py-2 px-2 rounded-lg transition text-center shadow-sm">
                                            Pesan
                                        </a>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>

    </div>
</div>

<?php 
include LAYOUTS_PATH . '/footer.php'; 
?>