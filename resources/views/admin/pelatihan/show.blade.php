<x-layouts.app :title="$pelatihan->nama">
    <x-page-header :title="$pelatihan->nama" :subtitle="$pelatihan->penyelenggaraLabel()" :back="route('admin.pelatihan.index')">
        <x-button :href="route('admin.pelatihan.edit', $pelatihan)" variant="secondary">Ubah</x-button>
        @can('kelola-peserta-pelatihan')
            <x-button :href="route('admin.peserta.pelatihan', $pelatihan)">Kelola Peserta</x-button>
        @endcan
    </x-page-header>

    <div class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-5">
        @foreach (\App\Enums\StatusPeserta::cases() as $s)
            <x-stat :label="$s->label()" :value="$jumlah[$s->value] ?? 0" :color="$s->color()" />
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <x-card title="Detail" class="lg:col-span-2">
            <dl class="divide-y divide-slate-100">
                <x-detail label="Kategori">{{ $pelatihan->kategori->nama }}</x-detail>
                <x-detail label="Tanggal">{{ $pelatihan->tanggal_mulai->translatedFormat('d F Y') }}{{ $pelatihan->tanggal_selesai ? ' s.d. '.$pelatihan->tanggal_selesai->translatedFormat('d F Y') : '' }}</x-detail>
                <x-detail label="Jam">{{ $pelatihan->jamLabel() }}</x-detail>
                <x-detail label="Lokasi">{{ $pelatihan->alamat }}, Kec. {{ $pelatihan->kecamatan->nama }}</x-detail>
                <x-detail label="Kuota">{{ $pelatihan->kuota }} orang</x-detail>
                <x-detail label="Batas pendaftaran">{{ $pelatihan->batas_pendaftaran->translatedFormat('d F Y') }}</x-detail>
                <x-detail label="Sertifikat">{{ $pelatihan->bersertifikat ? 'Ada' : 'Tidak ada' }}</x-detail>
                <x-detail label="Deskripsi">{{ $pelatihan->deskripsi }}</x-detail>
                <x-detail label="Syarat">{{ $pelatihan->syarat }}</x-detail>
            </dl>
            @if ($pelatihan->status === \App\Enums\StatusPelatihan::Dibuka)
                <a href="{{ route('public.pelatihan.show', $pelatihan) }}" target="_blank" class="mt-3 inline-block text-sm text-brand-600 hover:underline">Lihat di halaman publik &rarr;</a>
            @endif
        </x-card>

        <div class="space-y-4">
            <x-card title="Status Pelatihan">
                <p class="mb-3"><x-status :status="$pelatihan->status" class="text-sm" /></p>
                <form method="POST" action="{{ route('admin.pelatihan.status', $pelatihan) }}" class="space-y-2">
                    @csrf
                    <x-select name="status" :options="\App\Enums\StatusPelatihan::options()" :value="$pelatihan->status" :placeholder="false" />
                    <x-button class="w-full" variant="secondary">Ubah Status</x-button>
                </form>
                <p class="mt-2 text-xs text-slate-500">Draft &rarr; Dibuka (tampil publik) &rarr; Ditutup (pendaftaran tutup) &rarr; Selesai.</p>
            </x-card>
            @if ($pelatihan->posterUrl())
                <x-card title="Poster"><img src="{{ $pelatihan->posterUrl() }}" alt="Poster {{ $pelatihan->nama }}" class="w-full rounded-lg"></x-card>
            @endif
        </div>
    </div>
</x-layouts.app>
