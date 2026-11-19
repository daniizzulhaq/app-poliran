{{--
    Isian data instansi (dipakai instansi saat verifikasi dan petugas saat input jemput bola).
    Variabel: $instansi (nullable), $jenisOptions, $dokumenOptions, $namaPj, $waDefault, $petugas (bool)
--}}
@php($petugas = $petugas ?? false)
<x-card title="Data Instansi / Usaha">
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Nama instansi / usaha" name="nama" :value="$instansi?->nama" required />
        <x-select label="Jenis" name="jenis" :options="$jenisOptions" :value="$instansi?->jenis" required />
        <x-input label="Nama penanggung jawab" name="penanggung_jawab" :value="$instansi?->penanggung_jawab ?? $namaPj" required />
        <x-input label="No. WhatsApp" name="no_wa" :value="$instansi?->no_wa ?? $waDefault" required />
        <x-textarea label="Alamat lengkap" name="alamat" :value="$instansi?->alamat" required class="sm:col-span-2" />
    </div>
</x-card>

<x-card title="Dokumen">
    <div class="grid gap-4 sm:grid-cols-2">
        <x-select label="Jenis dokumen" name="jenis_dokumen" :options="$dokumenOptions" :value="$instansi?->jenis_dokumen" required />
        <x-input label="Nomor KTP / NIB" name="nomor_dokumen" :value="$instansi?->nomor_dokumen" required />
        <x-file-input label="Upload KTP atau NIB" name="file_dokumen" accept=".jpg,.jpeg,.png,.pdf" :required="! $instansi"
            hint="Foto/PDF, maksimal 4 MB." :current="$instansi ? route('berkas.instansi', [$instansi, 'dokumen']) : null" />
        <x-file-input :label="$petugas ? 'Foto penanggung jawab memegang KTP (diambil petugas)' : 'Foto selfie memegang KTP'" name="file_selfie" accept=".jpg,.jpeg,.png" :required="! $instansi"
            hint="Wajah dan KTP harus terlihat jelas. Maksimal 4 MB." :current="$instansi ? route('berkas.instansi', [$instansi, 'selfie']) : null" />
    </div>
</x-card>

<x-card title="Lokasi Usaha">
    <x-map-picker label="Pin lokasi usaha" :lat="$instansi?->latitude" :lng="$instansi?->longitude" />
</x-card>
