{{-- Detail lowongan (dipakai admin & instansi). Variabel: $lowongan --}}
<dl class="divide-y divide-slate-100">
    <x-detail label="Kategori">{{ $lowongan->kategori->nama }}</x-detail>
    <x-detail label="Skill dibutuhkan">{{ $lowongan->skills->pluck('nama')->implode(', ') }}</x-detail>
    <x-detail label="Tipe kerja">{{ $lowongan->tipe_kerja->label() }}</x-detail>
    <x-detail label="Lokasi kerja">{{ $lowongan->lokasi_kerja }}</x-detail>
    <x-detail label="Gaji">{{ $lowongan->gajiLabel() }}</x-detail>
    <x-detail label="Kuota / tersalur">{{ $lowongan->kuota }} orang / {{ $lowongan->jumlah_tersalur }} tersalur</x-detail>
    <x-detail label="Batas tanggal">{{ $lowongan->batas_tanggal->translatedFormat('d F Y') }}</x-detail>
    <x-detail label="Jobdesk">{{ $lowongan->jobdesk }}</x-detail>
    <x-detail label="Pendidikan minimal">{{ $lowongan->pendidikan_min?->label() ?? 'Tanpa syarat' }}</x-detail>
    <x-detail label="Umur">{{ $lowongan->umurLabel() }}</x-detail>
    <x-detail label="Jenis kelamin">{{ $lowongan->jenis_kelamin?->label() ?? 'Laki-laki / Perempuan' }}</x-detail>
    <x-detail label="Pengalaman">{{ $lowongan->pengalaman }}</x-detail>
    <x-detail label="Syarat lain">{{ $lowongan->syarat_lain }}</x-detail>
    <x-detail label="Fasilitas">{{ $lowongan->fasilitas }}</x-detail>
    <x-detail label="Ketentuan lain">{{ $lowongan->ketentuan_lain }}</x-detail>
</dl>
