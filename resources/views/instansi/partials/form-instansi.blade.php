{{--
    Isian data instansi (dipakai instansi saat verifikasi dan petugas saat input jemput bola).
    Variabel: $instansi (nullable), $jenisOptions, $namaPj, $waDefault, $tanggalLahirDefault (opsional)
--}}
<x-card title="Data Instansi / Usaha">
    <x-syarat-usia penyedia class="mb-4" />
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Nama instansi / usaha" name="nama" :value="$instansi?->nama" required />
        <x-select label="Jenis" name="jenis" :options="$jenisOptions" :value="$instansi?->jenis" required />
        <x-input label="Nama penanggung jawab" name="penanggung_jawab" :value="$instansi?->penanggung_jawab ?? $namaPj" required hint="Tulis nama lengkap sesuai KTP." />
        <x-input label="Tanggal lahir penanggung jawab" name="tanggal_lahir" type="date" :value="$instansi?->tanggal_lahir?->format('Y-m-d') ?? $tanggalLahirDefault ?? null" :max="today()->subYearsNoOverflow(18)->toDateString()" required hint="Isi sesuai KTP. Usia minimal 18 tahun." />
        <x-input label="No. WhatsApp" name="no_wa" :value="$instansi?->no_wa ?? $waDefault" required />
        <x-textarea label="Alamat lengkap" name="alamat" :value="$instansi?->alamat" required class="sm:col-span-2" />
    </div>
</x-card>

<x-card title="Lokasi Usaha">
    <x-map-picker label="Pin lokasi usaha" :lat="$instansi?->latitude" :lng="$instansi?->longitude" />
</x-card>
