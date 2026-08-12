<!-- pages/404.php — Halaman Error 404 -->
<div class="min-h-[70vh] bg-[#f4ece1] flex items-center justify-center px-6 py-12">
    <div class="text-center max-w-md">
        <div class="mb-8">
            <svg class="w-24 h-24 mx-auto text-[#8a5d49]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1 class="font-display text-6xl font-bold text-[#201913] mb-4">404</h1>
        <h2 class="font-display text-2xl font-bold text-[#201913] mb-2">Halaman Tidak Ditemukan</h2>

        <p class="text-[#66574b] mb-8">
            Maaf, halaman yang Anda cari tidak tersedia atau URL tidak valid.
        </p>

        <div class="flex flex-wrap gap-4 justify-center">
            <a href="<?= route('home') ?>" class="inline-block bg-[#8a5d49] hover:bg-[#734d3d] text-white font-bold py-3 px-6 rounded-full transition">
                ← Kembali ke Beranda
            </a>
            <a href="<?= route('dashboard') ?>" class="inline-block border border-[#8a5d49] text-[#8a5d49] hover:bg-[#8a5d49] hover:text-white font-bold py-3 px-6 rounded-full transition">
                Dashboard →
            </a>
        </div>
    </div>
</div>
