<x-layouts.app title="Dashboard">
    <x-page-header :title="'Selamat datang, '.$user->name" :subtitle="$kecamatanTugas ? 'Kecamatan tugas: '.$kecamatanTugas : 'Ringkasan data POLIRAN.'" />

    @isset($ringkasan)
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-5">
            <x-stat label="Lowongan aktif" :value="$ringkasan['lowongan_aktif']" color="green" :href="route('admin.lowongan.index')" />
            <x-stat label="Antrian survei instansi" :value="$ringkasan['antrian_instansi']" color="yellow" :href="route('admin.survei-instansi.index')" />
            <x-stat label="Antrian survei lowongan" :value="$ringkasan['antrian_lowongan']" color="yellow" :href="route('admin.survei-lowongan.index')" />
            <x-stat label="Pelatihan dibuka" :value="$ringkasan['pelatihan_dibuka'].' / '.$ringkasan['pelatihan_total']" color="purple" :href="route('admin.pelatihan.index')" />
            <x-stat label="Instansi terverifikasi" :value="$ringkasan['instansi_terverifikasi']" color="blue" />
        </div>
    @endisset

    @isset($perStatus)
        <h2 class="mb-2 text-sm font-semibold text-slate-600 uppercase">Pendaftar per status</h2>
        <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
            @foreach (\App\Enums\StatusPendaftar::cases() as $s)
                <x-stat :label="$s->label()" :value="$perStatus[$s->value]" :color="$s->color()" :href="route('admin.pendaftar.index', ['status' => $s->value])" />
            @endforeach
        </div>
        <h2 class="mb-2 text-sm font-semibold text-slate-600 uppercase">Perlu ditindaklanjuti</h2>
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-stat :label="'Pendaftar belum diproses > '.\App\Models\PencariKerja::BATAS_HARI_MENUNGGU.' hari'" :value="$menungguLama" :color="$menungguLama ? 'red' : 'gray'" :href="route('admin.pendaftar.index', ['status' => 'menunggu_lama'])" />
            <x-stat label="Penyaluran berjalan" :value="$penyaluranBerjalan" color="yellow" :href="route('admin.penyaluran.index')" />
            <x-stat label="Tindak lanjut kerja jatuh tempo" :value="$tindakLanjutJatuhTempo" :color="$tindakLanjutJatuhTempo ? 'red' : 'gray'" :href="route('admin.penyaluran.tindak-lanjut')" />
            <x-stat label="Peserta pelatihan menunggu seleksi" :value="$pesertaMenunggu" color="yellow" :href="route('admin.peserta.index')" />
        </div>
    @endisset

    <div class="grid gap-4 lg:grid-cols-2">
        @isset($rekap)
            <x-card title="Pendaftar per Kecamatan" :padding="false" class="lg:col-span-2">
                <x-slot:actions><x-button :href="route('admin.laporan.index')" size="sm" variant="secondary">Laporan & Export</x-button></x-slot:actions>
                <div class="overflow-x-auto">@include('admin.laporan._tabel-rekap')</div>
            </x-card>
        @endisset

        @isset($antrianInstansi)
            <x-card :title="'Antrian Survei Instansi ('.$jumlahAntrianInstansi.')'">
                @forelse ($antrianInstansi as $item)
                    <a href="{{ route('admin.survei-instansi.show', $item) }}" class="flex justify-between gap-2 border-b border-slate-100 py-2 text-sm last:border-0 hover:bg-slate-50">
                        <span class="font-medium">{{ $item->nama }}</span>
                        <span class="text-xs text-slate-500">{{ $item->diajukan_pada?->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">Tidak ada antrian.</p>
                @endforelse
            </x-card>
        @endisset

        @isset($antrianLowongan)
            <x-card :title="'Antrian Survei Lowongan ('.$jumlahAntrianLowongan.')'">
                @forelse ($antrianLowongan as $item)
                    <a href="{{ route('admin.lowongan.show', $item) }}" class="flex justify-between gap-2 border-b border-slate-100 py-2 text-sm last:border-0 hover:bg-slate-50">
                        <span><span class="font-medium">{{ $item->judul }}</span> <span class="text-xs text-slate-500">· {{ $item->instansi->nama }}</span></span>
                        <span class="text-xs text-slate-500">{{ $item->updated_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">Tidak ada antrian.</p>
                @endforelse
            </x-card>
        @endisset

        @isset($pendaftarBaru)
            <x-card title="Pendaftar Baru">
                <x-slot:actions><x-button :href="route('admin.pendaftar.create')" size="sm">+ Tambah Manual</x-button></x-slot:actions>
                @forelse ($pendaftarBaru as $item)
                    <a href="{{ route('admin.pendaftar.show', $item) }}" class="flex justify-between gap-2 border-b border-slate-100 py-2 text-sm last:border-0 hover:bg-slate-50">
                        <span><span class="font-medium">{{ $item->nama }}</span> <span class="text-xs text-slate-500">· {{ $item->kecamatan->nama }}</span></span>
                        <span class="flex items-center gap-2 text-xs text-slate-500">{{ $item->created_at->diffForHumans() }} <x-status :status="$item->status" /></span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">Belum ada pendaftar.</p>
                @endforelse
            </x-card>
        @endisset
    </div>
</x-layouts.app>
