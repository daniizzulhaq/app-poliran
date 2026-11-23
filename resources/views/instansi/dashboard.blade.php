<x-layouts.app title="Dashboard Instansi">
    <x-page-header title="Dashboard Instansi" :subtitle="$instansi?->nama" />

    @if (! $instansi)
        <x-card>
            <p class="font-semibold">Langkah berikutnya: lengkapi data verifikasi</p>
            <p class="mt-1 text-sm text-slate-600">Isi data instansi, nama dan tanggal lahir penanggung jawab sesuai KTP, serta pin lokasi usaha. Penanggung jawab harus berusia minimal 18 tahun dan sudah memiliki KTP. Petugas Polres akan melakukan survei sebelum Anda bisa mengunggah lowongan.</p>
            <x-button :href="route('instansi.verifikasi.edit')" class="mt-4">Lengkapi Data Verifikasi</x-button>
        </x-card>
    @else
        <x-card title="Status Verifikasi" class="mb-4">
            <div class="flex flex-wrap items-center gap-3">
                <x-status :status="$instansi->status" class="text-sm" />
                @switch($instansi->status)
                    @case(\App\Enums\StatusInstansi::MenungguSurvey)
                        <span class="text-sm text-slate-600">Data Anda sedang menunggu survei petugas Polres.</span>
                        @break
                    @case(\App\Enums\StatusInstansi::Terverifikasi)
                        <span class="text-sm text-slate-600">Instansi Anda sudah terverifikasi. Anda bisa mengunggah lowongan.</span>
                        @break
                    @case(\App\Enums\StatusInstansi::Ditolak)
                        <span class="text-sm text-slate-600">Pengajuan ditolak.</span>
                        @break
                @endswitch
            </div>
            @if ($instansi->status === \App\Enums\StatusInstansi::Ditolak)
                <div class="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-800"><strong>Alasan:</strong> {{ $instansi->alasan_tolak }}</div>
                <x-button :href="route('instansi.verifikasi.edit')" class="mt-3">Perbaiki & Kirim Ulang</x-button>
            @endif
            @if ($instansi->catatan_survei && $instansi->status !== \App\Enums\StatusInstansi::MenungguSurvey)
                <p class="mt-3 text-sm text-slate-600"><strong>Catatan petugas:</strong> {{ $instansi->catatan_survei }}</p>
            @endif
        </x-card>

        @isset($ringkasan)
            @include('instansi.partials.ringkasan-lowongan')
        @endisset
    @endif
</x-layouts.app>
