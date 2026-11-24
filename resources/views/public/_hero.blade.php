<section class="poliran-hero relative overflow-hidden bg-brand-900 text-white">
    <div class="hero-grid" aria-hidden="true"></div>
    <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 pt-12 pb-10 sm:pt-16 lg:grid-cols-2 lg:gap-8 lg:pt-16 lg:pb-14">
        <div class="relative z-10">
            <span class="inline-flex items-center gap-2 rounded-full border border-gold-400/30 bg-gold-400/10 px-3 py-2 text-[10px] font-bold tracking-[0.16em] text-gold-400 uppercase sm:text-xs"><span class="h-1.5 w-1.5 rounded-full bg-gold-400" aria-hidden="true"></span> Bersama membangun masa depan</span>
            <p class="mt-7 text-xs font-semibold tracking-[0.25em] text-brand-200 uppercase">POLIRAN · Polres TTS</p>
            <h1 class="hero-title mt-3 max-w-xl text-4xl font-extrabold text-white sm:text-6xl">{{ $konten['hero_judul'] }}</h1>
            <div class="mt-6 h-1 w-16 rounded-full bg-gold-400" aria-hidden="true"></div>
            <p class="mt-6 max-w-lg text-base leading-relaxed text-brand-100 sm:text-lg">{{ $konten['hero_teks'] }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <x-button href="#lowongan" variant="gold" size="lg">Lihat Pekerjaan Tersedia <span aria-hidden="true">&rarr;</span></x-button>
                <a href="#pelatihan" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-6 py-3 text-base font-semibold text-white transition hover:bg-white/10">Lihat Pelatihan Tersedia</a>
            </div>
            <div class="mt-7 flex flex-wrap gap-x-5 gap-y-2 text-xs text-brand-100">
                <span class="flex items-center gap-2"><x-icon name="shield" class="h-4 w-4 text-gold-400" /> Didampingi petugas</span>
                <span class="flex items-center gap-2"><x-icon name="check" class="h-4 w-4 text-gold-400" /> Gratis · Tanpa Biaya Apa Pun</span>
            </div>
        </div>
        <div class="hero-portrait-area relative mx-auto w-full max-w-md lg:ml-auto">
            <div class="hero-orbit" aria-hidden="true"></div>
            <div class="hero-portrait">
                <img src="{{ $fotoKapolres }}" alt="Kapolres Timor Tengah Selatan" fetchpriority="high" width="1280" height="1280" @class(['officer-photo', 'officer-photo-original' => ! $konten['sambutan_foto']])>
            </div>
            <div class="absolute right-0 bottom-0 left-0 flex items-center gap-3 rounded-2xl border border-white/15 bg-brand-800 px-5 py-4 shadow-xl">
                <img src="{{ asset('images/logo-polres-tts.png') }}" alt="" width="38" height="44" class="h-11 w-9 object-contain">
                <div><p class="text-sm font-bold text-white">Kapolres Timor Tengah Selatan</p><p class="mt-1 text-xs text-brand-100">Melindungi, mengayomi, dan melayani</p></div>
                <span class="ml-auto text-2xl text-gold-400" aria-hidden="true">&ldquo;</span>
            </div>
        </div>
    </div>
    <div class="relative border-t border-white/10 bg-white/5">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-6 px-4 py-7 sm:grid-cols-4 sm:gap-8">
            @foreach ([['terdaftar', 'warga terdaftar'], ['bersertifikat', 'warga bersertifikat'], ['disalurkan', 'warga sudah disalurkan kerja'], ['instansi', 'instansi terverifikasi']] as [$kunci, $label])
                <div class="border-l-2 border-gold-400/50 pl-4"><p class="text-3xl font-extrabold text-white">{{ number_format($statistik[$kunci], 0, ',', '.') }}<span class="ml-1 text-sm text-gold-400" aria-hidden="true">&bull;</span></p><p class="mt-1 text-xs leading-relaxed text-brand-100">{{ $label }}</p></div>
            @endforeach
        </div>
    </div>
</section>
