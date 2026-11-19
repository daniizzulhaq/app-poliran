<x-layouts.public>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 text-white">
        <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-gold-400/20 blur-3xl"></div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-10 px-4 py-16 sm:py-24 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <span class="inline-block rounded-full bg-gold-400 px-3 py-1 text-xs font-bold tracking-wide text-brand-900 uppercase">Gratis · Tanpa Biaya Apa Pun</span>
                <h1 class="mt-4 text-4xl leading-tight font-extrabold text-white sm:text-5xl">{{ $konten['hero_judul'] }}</h1>
                <p class="mt-4 max-w-xl text-lg text-brand-100">{{ $konten['hero_teks'] }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <x-button :href="route('register')" variant="gold" size="lg">Daftar Sekarang</x-button>
                    <x-button :href="route('public.pelatihan.index')" variant="secondary" size="lg">Lihat Pelatihan</x-button>
                </div>
            </div>
            <div class="hidden lg:col-span-2 lg:block">
                <div class="rounded-2xl bg-white/10 p-6 ring-1 ring-white/20 backdrop-blur">
                    <p class="text-sm text-brand-100">Sampai hari ini</p>
                    <p class="text-5xl font-extrabold text-gold-400">{{ number_format($statistik['disalurkan'], 0, ',', '.') }}</p>
                    <p class="text-lg font-semibold">warga sudah disalurkan kerja</p>
                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div><p class="text-2xl font-bold">{{ number_format($statistik['terdaftar'], 0, ',', '.') }}</p><p class="text-brand-100">warga terdaftar</p></div>
                        <div><p class="text-2xl font-bold">{{ number_format($statistik['bersertifikat'], 0, ',', '.') }}</p><p class="text-brand-100">bersertifikat</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Untuk siapa --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <h2 class="text-center text-2xl font-extrabold sm:text-3xl">Untuk Siapa POLIRAN?</h2>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['🏢', 'Penyedia Kerja', 'Perusahaan, UMKM, perorangan, atau instansi pemerintah yang butuh pekerja. Data Anda disurvei petugas agar aman.', 'Daftar sebagai Instansi', route('register.instansi')],
                ['🎓', 'Penyedia Pelatihan', 'Pelatihan diselenggarakan oleh Polres TTS bersama mitra. Tidak perlu membuat akun untuk melihat jadwal pelatihan.', 'Lihat Pelatihan', route('public.pelatihan.index')],
                ['🧑‍🔧', 'Pencari Kerja', 'Warga TTS yang ingin bekerja. Daftar, lengkapi profil, dan petugas di kecamatan Anda akan membantu.', 'Daftar Pencari Kerja', route('register.pencari-kerja')],
                ['📚', 'Pencari Pelatihan', 'Ingin menambah keterampilan? Pilih pelatihan yang dibuka dan daftar langsung dengan akun Anda.', 'Lihat & Daftar Pelatihan', route('public.pelatihan.index')],
            ] as [$ikon, $judul, $teks, $tombol, $url])
                <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <span class="text-4xl">{{ $ikon }}</span>
                    <h3 class="mt-3 text-lg font-bold">{{ $judul }}</h3>
                    <p class="mt-2 flex-1 text-sm text-slate-600">{{ $teks }}</p>
                    <x-button :href="$url" variant="secondary" class="mt-5">{{ $tombol }}</x-button>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Statistik --}}
    <section class="bg-brand-50 py-14">
        <div class="mx-auto max-w-6xl px-4">
            <h2 class="text-center text-2xl font-extrabold sm:text-3xl">Hasil Program</h2>
            <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-5">
                @foreach ([['terdaftar', 'warga terdaftar'], ['dilatih', 'warga dilatih'], ['bersertifikat', 'warga bersertifikat'], ['disalurkan', 'warga disalurkan kerja'], ['instansi', 'instansi terverifikasi']] as [$kunci, $label])
                    <div class="rounded-2xl bg-white p-5 text-center shadow-sm">
                        <p class="text-3xl font-extrabold text-brand-700">{{ number_format($statistik[$kunci], 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Sambutan Kapolres --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="grid items-center gap-8 md:grid-cols-3">
            <div class="mx-auto w-48 md:w-full md:max-w-xs">
                @if ($konten['sambutan_foto'])
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($konten['sambutan_foto']) }}" alt="Foto {{ $konten['sambutan_nama'] }}" class="aspect-[3/4] w-full rounded-2xl object-cover shadow-lg">
                @else
                    <div class="flex aspect-[3/4] w-full items-center justify-center rounded-2xl bg-brand-100 text-6xl">👮</div>
                @endif
            </div>
            <div class="md:col-span-2">
                <p class="text-sm font-semibold tracking-wide text-gold-600 uppercase">Sambutan Kapolres</p>
                <blockquote class="mt-3 text-lg whitespace-pre-line text-slate-700">{{ $konten['sambutan_isi'] }}</blockquote>
                <p class="mt-5 font-bold">{{ $konten['sambutan_nama'] }}</p>
                <p class="text-sm text-slate-500">{{ $konten['sambutan_pangkat'] }} · Kapolres Timor Tengah Selatan</p>
            </div>
        </div>
    </section>

    {{-- Program & alur --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-6xl px-4">
            <h2 class="text-2xl font-extrabold sm:text-3xl">Tentang Program</h2>
            <p class="mt-3 max-w-3xl whitespace-pre-line text-slate-700">{{ $konten['program_teks'] }}</p>
            <div class="mt-8">@include('public._alur')</div>
        </div>
    </section>

    {{-- Pelatihan dibuka --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h2 class="text-2xl font-extrabold sm:text-3xl">Pelatihan yang Sedang Dibuka</h2>
            <a href="{{ route('public.pelatihan.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">Lihat semua &rarr;</a>
        </div>
        @if ($pelatihan->isEmpty())
            <x-empty class="mt-6" message="Belum ada pelatihan yang dibuka. Silakan cek kembali nanti." />
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pelatihan as $item)
                    <x-pelatihan-card :pelatihan="$item" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Cara daftar --}}
    <section class="bg-brand-900 py-16 text-white" x-data="{ tab: 'pencari' }">
        <div class="mx-auto max-w-4xl px-4">
            <h2 class="text-center text-2xl font-extrabold text-white sm:text-3xl">Cara Daftar</h2>
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                @foreach (['pencari' => 'Pencari Kerja', 'pelatihan' => 'Pencari Pelatihan', 'instansi' => 'Penyedia Kerja'] as $kunci => $label)
                    <button type="button" @click="tab = '{{ $kunci }}'" :class="tab === '{{ $kunci }}' ? 'bg-gold-400 text-brand-900' : 'bg-white/10 text-white hover:bg-white/20'" class="rounded-full px-4 py-2 text-sm font-semibold">{{ $label }}</button>
                @endforeach
            </div>
            <div class="mt-8 rounded-2xl bg-white p-6 text-slate-800">
                <ol x-show="tab === 'pencari'" class="list-decimal space-y-2 pl-5">
                    <li>Klik <strong>Daftar</strong> lalu pilih <strong>Pencari Kerja</strong>.</li>
                    <li>Isi nama, email, nomor WhatsApp, dan password. Centang persetujuan data.</li>
                    <li>Lengkapi profil: NIK, alamat (kecamatan & desa di TTS), minat kerja, kemampuan, dan CV bila ada.</li>
                    <li>Petugas Polres di kecamatan Anda akan mendata dan menghubungi Anda. Pantau status di dashboard.</li>
                </ol>
                <ol x-show="tab === 'pelatihan'" x-cloak class="list-decimal space-y-2 pl-5">
                    <li>Buka menu <strong>Pelatihan</strong> dan pilih pelatihan yang dibuka.</li>
                    <li>Klik <strong>Daftar</strong>. Masuk dengan akun pencari kerja (belum punya? daftar dulu).</li>
                    <li>Data terisi otomatis dari profil. Tulis alasan ikut pelatihan dan pengalaman Anda.</li>
                    <li>Tunggu hasil seleksi di dashboard. Peserta yang lulus bisa mengunduh sertifikat.</li>
                </ol>
                <ol x-show="tab === 'instansi'" x-cloak class="list-decimal space-y-2 pl-5">
                    <li>Klik <strong>Daftar</strong> lalu pilih <strong>Penyedia Kerja</strong>.</li>
                    <li>Lengkapi data instansi, upload KTP/NIB, foto selfie memegang KTP, dan pin lokasi usaha.</li>
                    <li>Petugas Polres melakukan survei. Hasilnya tampil di dashboard Anda.</li>
                    <li>Setelah terverifikasi, upload lowongan. Lowongan diperiksa petugas, lalu petugas menyalurkan pekerja kepada Anda.</li>
                </ol>
            </div>
        </div>
    </section>

    {{-- Waspada calo & TPPO --}}
    <section id="waspada" class="mx-auto max-w-6xl px-4 py-16">
        @include('public._waspada')
    </section>

    {{-- Cerita sukses --}}
    @if ($testimoni->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 py-16">
            <h2 class="text-center text-2xl font-extrabold sm:text-3xl">Cerita Sukses</h2>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach ($testimoni as $item)
                    <figure class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <blockquote class="text-slate-700">&ldquo;{{ $item->isi }}&rdquo;</blockquote>
                        <figcaption class="mt-4 flex items-center gap-3">
                            @if ($item->fotoUrl())
                                <img src="{{ $item->fotoUrl() }}" alt="Foto {{ $item->nama }}" class="h-12 w-12 rounded-full object-cover">
                            @else
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 font-bold text-brand-700">{{ mb_substr($item->nama, 0, 1) }}</span>
                            @endif
                            <span><span class="block font-semibold">{{ $item->nama }}</span><span class="text-sm text-slate-500">{{ $item->keterangan }}</span></span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Kontak --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-6xl px-4">@include('public._kontak')</div>
    </section>
</x-layouts.public>
