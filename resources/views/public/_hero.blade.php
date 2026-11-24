<section class="poliran-hero relative overflow-hidden bg-brand-900 text-white">
    <div class="hero-grid" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl px-4 py-14 sm:py-20">
        <div class="relative z-10 mx-auto flex max-w-3xl flex-col items-center text-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-gold-400/30 bg-gold-400/10 px-3 py-2 text-[10px] font-bold tracking-[0.16em] text-gold-400 uppercase sm:text-xs"><span class="h-1.5 w-1.5 rounded-full bg-gold-400" aria-hidden="true"></span> Bersama membangun masa depan</span>
            <p class="mt-7 text-xs font-semibold tracking-[0.25em] text-brand-200 uppercase">POLIRAN · Polres TTS</p>
            <h1 class="hero-title mt-3 max-w-3xl text-4xl font-extrabold text-white sm:text-6xl">{{ $konten['hero_judul'] }}</h1>
            <div class="mt-6 h-1 w-16 rounded-full bg-gold-400" aria-hidden="true"></div>
            <p class="mt-6 max-w-2xl text-base leading-relaxed text-brand-100 sm:text-lg">{{ $konten['hero_teks'] }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <x-button href="#lowongan" variant="gold" size="lg">Lihat Pekerjaan Tersedia <span aria-hidden="true">&rarr;</span></x-button>
                <a href="#pelatihan" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-6 py-3 text-base font-semibold text-white transition hover:bg-white/10">Lihat Pelatihan Tersedia</a>
            </div>
            <div class="mt-7 flex flex-wrap justify-center gap-x-5 gap-y-2 text-xs text-brand-100">
                <span class="flex items-center gap-2"><x-icon name="shield" class="h-4 w-4 text-gold-400" /> Didampingi petugas</span>
                <span class="flex items-center gap-2"><x-icon name="check" class="h-4 w-4 text-gold-400" /> Gratis · Tanpa Biaya Apa Pun</span>
            </div>
        </div>
    </div>
</section>
