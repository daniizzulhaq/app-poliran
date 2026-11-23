<x-layouts.public :title="$pelatihan->nama" :description="\Illuminate\Support\Str::limit($pelatihan->deskripsi, 150)">
    <section class="mx-auto max-w-6xl px-4 py-10">
        <a href="{{ route('public.pelatihan.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Semua pelatihan</a>
        <div class="mt-4 grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-3 flex flex-wrap gap-1">
                    <x-badge color="blue">{{ $pelatihan->kategori->nama }}</x-badge>
                    @if ($pelatihan->bersertifikat)<x-badge color="green">Bersertifikat</x-badge>@endif
                    <x-badge color="yellow">Gratis</x-badge>
                    <x-status :status="$pelatihan->status" />
                </div>
                <h1 class="text-3xl font-extrabold">{{ $pelatihan->nama }}</h1>
                <p class="mt-1 text-slate-600">Penyelenggara: {{ $pelatihan->penyelenggaraLabel() }}</p>
                @if ($pelatihan->posterUrl())
                    <img src="{{ $pelatihan->posterUrl() }}" alt="Poster {{ $pelatihan->nama }}" class="mt-6 w-full rounded-xl">
                @endif
                <h2 class="mt-8 mb-2 text-xl font-bold">Tentang pelatihan</h2>
                <p class="whitespace-pre-line text-slate-700">{{ $pelatihan->deskripsi }}</p>
                @if ($pelatihan->syarat)
                    <h2 class="mt-6 mb-2 text-xl font-bold">Syarat peserta</h2>
                    <p class="whitespace-pre-line text-slate-700">{{ $pelatihan->syarat }}</p>
                @endif
            </div>
            <aside>
                <div class="sticky top-20 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <dl class="space-y-3 text-sm">
                        <div><dt class="text-slate-500">Tanggal</dt><dd class="font-medium">{{ $pelatihan->tanggal_mulai->translatedFormat('d F Y') }}{{ $pelatihan->tanggal_selesai ? ' - '.$pelatihan->tanggal_selesai->translatedFormat('d F Y') : '' }}</dd></div>
                        <div><dt class="text-slate-500">Jam</dt><dd class="font-medium">{{ $pelatihan->jamLabel() }}</dd></div>
                        <div><dt class="text-slate-500">Lokasi</dt><dd class="font-medium">{{ $pelatihan->alamat }}, Kec. {{ $pelatihan->kecamatan->nama }}</dd></div>
                        <div><dt class="text-slate-500">Kuota</dt><dd class="font-medium">{{ $pelatihan->kuota }} orang</dd></div>
                        <div><dt class="text-slate-500">Batas pendaftaran</dt><dd class="font-medium">{{ $pelatihan->batas_pendaftaran->translatedFormat('d F Y') }}</dd></div>
                        <div><dt class="text-slate-500">Biaya</dt><dd class="font-bold text-green-700">GRATIS</dd></div>
                    </dl>
                    <x-syarat-usia class="mt-4" />
                    @if ($pelatihan->bisaDidaftar())
                        <x-button :href="route('pencari.pelatihan.daftar', $pelatihan)" size="lg" class="mt-5 w-full">Daftar Pelatihan</x-button>
                        <p class="mt-2 text-center text-xs text-slate-500">Wajib masuk dengan akun pencari kerja. Belum punya akun? <a href="{{ route('register.pencari-kerja') }}" class="text-brand-600 underline">Daftar gratis</a></p>
                    @else
                        <p class="mt-5 rounded-lg bg-slate-100 p-3 text-center text-sm text-slate-600">Pendaftaran sudah ditutup.</p>
                    @endif
                </div>
            </aside>
        </div>
    </section>
</x-layouts.public>
