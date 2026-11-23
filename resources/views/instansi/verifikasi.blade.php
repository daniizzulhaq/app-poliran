<x-layouts.app title="Data Verifikasi Instansi">
    <x-page-header title="Data Verifikasi Instansi" subtitle="Data ini diperiksa dan disurvei petugas Polres sebelum Anda bisa mengunggah lowongan." />

    @if ($instansi)
        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
            Status verifikasi: <x-status :status="$instansi->status" />
        </div>
        @if ($instansi->status === \App\Enums\StatusInstansi::Ditolak)
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-semibold">Pengajuan ditolak. Alasan:</p>
                <p class="mt-1 whitespace-pre-line">{{ $instansi->alasan_tolak }}</p>
                <p class="mt-2">Silakan perbaiki data di bawah lalu kirim ulang.</p>
            </div>
        @endif
    @endif

    @if ($bisaUbah)
        <form method="POST" action="{{ route('instansi.verifikasi.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('instansi.partials.form-instansi', ['namaPj' => $user->name, 'waDefault' => $user->phone, 'tanggalLahirDefault' => $user->tanggal_lahir?->format('Y-m-d')])

            <x-button size="lg">{{ $instansi ? 'Kirim Ulang untuk Disurvei' : 'Kirim untuk Disurvei' }}</x-button>
        </form>
    @else
        <x-card title="Data Instansi">
            <dl class="divide-y divide-slate-100">
                <x-detail label="Nama">{{ $instansi->nama }}</x-detail>
                <x-detail label="Jenis">{{ $instansi->jenis->label() }}</x-detail>
                <x-detail label="Penanggung jawab">{{ $instansi->penanggung_jawab }}</x-detail>
                <x-detail label="Tanggal lahir penanggung jawab">{{ $instansi->tanggal_lahir?->translatedFormat('d F Y') ?? 'Belum diisi' }}</x-detail>
                <x-detail label="No. WhatsApp">{{ $instansi->no_wa }}</x-detail>
                <x-detail label="Alamat">{{ $instansi->alamat }}</x-detail>
            </dl>
            <x-map-view :lat="$instansi->latitude" :lng="$instansi->longitude" class="mt-4" />
            @if ($instansi->status === \App\Enums\StatusInstansi::MenungguSurvey)
                <p class="mt-4 text-sm text-slate-600">Data sedang menunggu survei petugas. Data tidak bisa diubah selama proses survei.</p>
            @endif
        </x-card>
    @endif
</x-layouts.app>
