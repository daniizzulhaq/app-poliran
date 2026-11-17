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
        <form method="POST" action="{{ route('instansi.verifikasi.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <x-card title="Data Instansi / Usaha">
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input label="Nama instansi / usaha" name="nama" :value="$instansi?->nama" required />
                    <x-select label="Jenis" name="jenis" :options="$jenisOptions" :value="$instansi?->jenis" required />
                    <x-input label="Nama penanggung jawab" name="penanggung_jawab" :value="$instansi?->penanggung_jawab ?? $user->name" required />
                    <x-input label="No. WhatsApp" name="no_wa" :value="$instansi?->no_wa ?? $user->phone" required />
                    <x-textarea label="Alamat lengkap" name="alamat" :value="$instansi?->alamat" required class="sm:col-span-2" />
                </div>
            </x-card>

            <x-card title="Dokumen">
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-select label="Jenis dokumen" name="jenis_dokumen" :options="$dokumenOptions" :value="$instansi?->jenis_dokumen" required />
                    <x-input label="Nomor KTP / NIB" name="nomor_dokumen" :value="$instansi?->nomor_dokumen" required />
                    <x-file-input label="Upload KTP atau NIB" name="file_dokumen" accept=".jpg,.jpeg,.png,.pdf" :required="! $instansi"
                        hint="Foto/PDF, maksimal 4 MB." :current="$instansi ? route('berkas.instansi', [$instansi, 'dokumen']) : null" />
                    <x-file-input label="Foto selfie memegang KTP" name="file_selfie" accept=".jpg,.jpeg,.png" :required="! $instansi"
                        hint="Wajah dan KTP harus terlihat jelas. Maksimal 4 MB." :current="$instansi ? route('berkas.instansi', [$instansi, 'selfie']) : null" />
                </div>
            </x-card>

            <x-card title="Lokasi Usaha">
                <x-map-picker label="Pin lokasi usaha" :lat="$instansi?->latitude" :lng="$instansi?->longitude" />
            </x-card>

            <x-button size="lg">{{ $instansi ? 'Kirim Ulang untuk Disurvei' : 'Kirim untuk Disurvei' }}</x-button>
        </form>
    @else
        <x-card title="Data Instansi">
            <dl class="divide-y divide-slate-100">
                <x-detail label="Nama">{{ $instansi->nama }}</x-detail>
                <x-detail label="Jenis">{{ $instansi->jenis->label() }}</x-detail>
                <x-detail label="Penanggung jawab">{{ $instansi->penanggung_jawab }}</x-detail>
                <x-detail label="No. WhatsApp">{{ $instansi->no_wa }}</x-detail>
                <x-detail label="Alamat">{{ $instansi->alamat }}</x-detail>
                <x-detail label="Dokumen">{{ $instansi->jenis_dokumen->label() }} - {{ $instansi->nomor_dokumen }}</x-detail>
            </dl>
            <x-map-view :lat="$instansi->latitude" :lng="$instansi->longitude" class="mt-4" />
            @if ($instansi->status === \App\Enums\StatusInstansi::MenungguSurvey)
                <p class="mt-4 text-sm text-slate-600">Data sedang menunggu survei petugas. Data tidak bisa diubah selama proses survei.</p>
            @endif
        </x-card>
    @endif
</x-layouts.app>
