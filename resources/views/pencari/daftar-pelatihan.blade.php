<x-layouts.app :title="'Daftar: '.$pelatihan->nama">
    <x-page-header :title="'Daftar Pelatihan: '.$pelatihan->nama" :back="route('public.pelatihan.show', $pelatihan)" />
    <x-syarat-usia class="mb-4" />

    @if ($sudah)
        <x-empty message="Anda sudah terdaftar di pelatihan ini.">
            <x-button :href="route('pencari.pelatihan.index')">Lihat Pelatihan Saya</x-button>
        </x-empty>
    @elseif (! $pelatihan->bisaDidaftar())
        <x-empty message="Pendaftaran pelatihan ini sudah ditutup." />
    @else
        <div class="grid gap-4 lg:grid-cols-3">
            <form method="POST" action="{{ route('pencari.pelatihan.store', $pelatihan) }}" class="space-y-4 lg:col-span-2">
                @csrf
                <x-card title="Data Anda (dari profil)">
                    <dl class="divide-y divide-slate-100">
                        <x-detail label="Nama">{{ $pencari->nama }}</x-detail>
                        <x-detail label="NIK"><span class="font-mono">{{ $pencari->nikTersensor() }}</span></x-detail>
                        <x-detail label="Jenis kelamin">{{ $pencari->jenis_kelamin->label() }}</x-detail>
                        <x-detail label="Umur">{{ $pencari->umur() }} tahun</x-detail>
                        <x-detail label="Pendidikan">{{ $pencari->pendidikan->label() }}</x-detail>
                        <x-detail label="No. WhatsApp">{{ $pencari->no_wa }}</x-detail>
                        <x-detail label="Alamat">Desa {{ $pencari->desa->nama }}, Kec. {{ $pencari->kecamatan->nama }}</x-detail>
                    </dl>
                    <a href="{{ route('pencari.profil.edit') }}" class="mt-2 inline-block text-sm text-brand-600 hover:underline">Data salah? Ubah profil</a>
                </x-card>
                <x-card title="Pertanyaan Pendaftaran">
                    <div class="space-y-4">
                        <x-textarea label="Mengapa Anda ingin ikut pelatihan ini?" name="alasan" rows="3" required />
                        <x-textarea label="Pengalaman Anda di bidang ini" name="pengalaman_bidang" rows="3" hint="Kosongkan bila belum ada pengalaman." />
                    </div>
                </x-card>
                <x-button size="lg">Kirim Pendaftaran</x-button>
            </form>
            <x-card title="Ringkasan Pelatihan">
                <dl class="divide-y divide-slate-100 text-sm">
                    <x-detail label="Tanggal">{{ $pelatihan->tanggal_mulai->translatedFormat('d F Y') }}</x-detail>
                    <x-detail label="Jam">{{ $pelatihan->jamLabel() }}</x-detail>
                    <x-detail label="Lokasi">{{ $pelatihan->alamat }}, Kec. {{ $pelatihan->kecamatan->nama }}</x-detail>
                    <x-detail label="Batas daftar">{{ $pelatihan->batas_pendaftaran->translatedFormat('d F Y') }}</x-detail>
                </dl>
            </x-card>
        </div>
    @endif
</x-layouts.app>
