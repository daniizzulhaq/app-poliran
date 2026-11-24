<x-layouts.public>
    @php
        $fotoKapolres = $konten['sambutan_foto']
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($konten['sambutan_foto'])
            : asset('images/kapolres-tts.jpg');
    @endphp
    @include('public._hero')
    {{-- Penjelasan POLIRAN --}}
    <section class="border-b border-slate-100 bg-[#faf9f6] py-12 sm:py-16">
        <div class="mx-auto grid max-w-6xl items-center gap-8 px-4 md:grid-cols-[1fr_280px]">
            <div>
                <p class="section-eyebrow">Hadir untuk warga TTS</p>
                <h2 class="mt-3 text-2xl font-extrabold sm:text-3xl">Tentang POLIRAN</h2>
                <p class="mt-4 max-w-3xl text-sm leading-relaxed whitespace-pre-line text-slate-600 sm:text-base">{{ $konten['program_teks'] }}</p>
                <a href="{{ route('public.tentang') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-brand-700 hover:underline">Kenali program kami <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="rounded-2xl border border-gold-500/20 bg-white p-6">
                <img src="{{ asset('images/logo-polisi-bersama-masyarakat.png') }}" alt="Polisi Bersama Masyarakat — Melindungi, Mengayomi dan Melayani" width="560" height="210" class="mx-auto w-full max-w-xs" loading="lazy">
                <p class="mt-4 border-t border-slate-100 pt-4 text-center text-[10px] font-semibold tracking-widest text-slate-500 uppercase">Polres Timor Tengah Selatan</p>
            </div>
        </div>
    </section>

    <section id="lowongan" class="mx-auto max-w-6xl scroll-mt-24 px-4 pt-14 pb-8 sm:pt-20">
        <p class="section-eyebrow mb-3">Kesempatan untuk Anda</p>
        @include('lowongan._tersedia')
    </section>

    {{-- Pelatihan dibuka --}}
    <section id="pelatihan" class="mx-auto max-w-6xl scroll-mt-24 px-4 pt-8 pb-14 sm:pb-20">
        <p class="section-eyebrow mb-3">Tumbuh bersama keterampilan baru</p>
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h2 class="text-2xl font-extrabold sm:text-3xl">Pelatihan yang Tersedia</h2>
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

    {{-- Untuk siapa --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <h2 class="text-center text-2xl font-extrabold sm:text-3xl">Untuk Siapa POLIRAN?</h2>
        <p class="mt-3 text-center text-slate-600">Pilih layanan sesuai kebutuhan Anda. Semua layanan gratis.</p>
        <x-syarat-usia class="mx-auto mt-4 max-w-3xl text-center" />
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['briefcase', 'Pencari Kerja', 'Warga TTS yang ingin bekerja. Daftar, lengkapi profil, dan petugas di kecamatan Anda akan membantu.', 'Daftar sebagai pencari kerja', route('register.pencari-kerja')],
                ['book', 'Pencari Pelatihan', 'Ingin menambah keterampilan? Pilih pelatihan yang dibuka dan daftar langsung dengan akun Anda.', 'Daftar sebagai pencari pelatihan', route('public.pelatihan.index')],
                ['building', 'Penyedia Kerja', 'Perusahaan, UMKM, perorangan, atau instansi pemerintah yang membutuhkan pekerja. Daftar sebagai penyedia kerja untuk membuka lowongan setelah verifikasi petugas.', 'Daftar sebagai penyedia kerja', route('register.instansi')],
            ] as [$ikon, $judul, $teks, $tombol, $url])
                <div class="service-card flex flex-col rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><x-icon :name="$ikon" /></span>
                    <h3 class="mt-5 text-lg font-bold">{{ $judul }}</h3>
                    <p class="mt-2 flex-1 text-sm text-slate-600">{{ $teks }}</p>
                    <x-button :href="$url" variant="secondary" class="mt-5">{{ $tombol }}</x-button>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Statistik --}}
    <section class="border-y border-slate-100 bg-[#faf9f6] py-14">
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
    <section class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
        <div class="grid items-center gap-8 rounded-3xl border border-slate-200 bg-slate-50 p-6 md:grid-cols-3 md:gap-12 md:p-10">
            <div class="officer-welcome relative mx-auto w-full max-w-xs overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <img src="{{ $fotoKapolres }}" alt="Foto Kapolres Timor Tengah Selatan" width="1280" height="1280" loading="lazy" @class(['officer-photo', 'officer-photo-original' => ! $konten['sambutan_foto']])>
            </div>
            <div class="md:col-span-2">
                <p class="section-eyebrow">Sambutan Kapolres</p>
                <span class="mt-3 block h-10 font-serif text-6xl text-gold-500" aria-hidden="true">&ldquo;</span>
                <blockquote class="mt-3 text-base leading-relaxed whitespace-pre-line text-slate-700 sm:text-lg">{{ $konten['sambutan_isi'] }}</blockquote>
                <p class="mt-6 font-bold">{{ $konten['sambutan_nama'] === 'Nama Kapolres' ? 'Kapolres Timor Tengah Selatan' : $konten['sambutan_nama'] }}</p>
                <p class="text-sm text-slate-500">{{ $konten['sambutan_pangkat'] }} · Kapolres Timor Tengah Selatan</p>
            </div>
        </div>
    </section>

    {{-- Alur program --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-6xl px-4">
            <h2 class="text-2xl font-extrabold sm:text-3xl">Alur Program POLIRAN</h2>
            <div class="mt-8">@include('public._alur')</div>
        </div>
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

</x-layouts.public>
