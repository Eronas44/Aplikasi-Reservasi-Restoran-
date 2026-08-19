<?php
/**
 * components/auth-background.php — Latar belakang home (blur) untuk halaman login & register.
 * Mengikuti style frontend lama (v1) yang menampilkan home sebagai latar yang di-blur.
 */
?>
<div id="home-bg" class="min-h-screen pointer-events-none select-none">
    <style>
        #home-bg {
            filter: blur(6px);
            transform: scale(1.03);
            transition: filter 0.3s ease;
        }
    </style>

    <!-- Hero -->
    <section class="grid md:grid-cols-2 gap-10 items-center px-16 py-20 bg-[#f4ece1]">
        <div>
            <span class="inline-block text-xs font-bold tracking-widest text-[#8a5d49] border border-[#8a5d49]/40 rounded-full px-4 py-1.5 mb-6">
                SISTEM RESERVASI RESTORAN
            </span>
            <h1 class="font-display text-5xl font-medium text-[#201913] leading-tight mb-2">
                Kemewahan Rasa di Setiap Sajian.
            </h1>
            <p class="text-[#66574b] text-base leading-relaxed mb-8 max-w-md">
                Rasa &amp; Cerita Dari meja reservasi hingga pesanan siap saji, menyatukan alur kerja restoran dalam satu genggaman. Tampil berkelas, layani lebih cepat.
            </p>
            <div class="flex gap-4">
                <div class="bg-[#5e392e] text-white text-sm font-bold px-7 py-3.5 rounded-2xl">Buat Akun</div>
                <div class="border border-[#5e392e] text-[#5e392e] text-sm font-bold px-7 py-3.5 rounded-2xl">Masuk ke Sistem</div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-4 shadow-xl">
            <div class="rounded-[1.5rem] overflow-hidden aspect-[4/3]">
                <img src="https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?q=80&w=1200&auto=format&fit=crop"
                     alt="" class="w-full h-full object-cover">
            </div>
        </div>
    </section>
</div>
