{{-- Field profil pendaftar. Variabel: $pencari (nullable), $namaDefault, $waDefault, opsi dari OpsiForm::pendaftar() --}}
<x-card title="Data Diri">
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Nama lengkap (sesuai KTP)" name="nama" :value="$pencari?->nama ?? $namaDefault" required />
        <x-input label="NIK" name="nik" :value="$pencari?->nik" required inputmode="numeric" maxlength="16" hint="16 digit sesuai KTP." />
        <x-select label="Jenis kelamin" name="jenis_kelamin" :options="$jenisKelaminOptions" :value="$pencari?->jenis_kelamin" required />
        <x-input label="Tanggal lahir" name="tanggal_lahir" type="date" :value="$pencari?->tanggal_lahir?->format('Y-m-d')" required />
        <x-select label="Pendidikan terakhir" name="pendidikan" :options="$pendidikanOptions" :value="$pencari?->pendidikan" required />
        <x-input label="Jurusan" name="jurusan" :value="$pencari?->jurusan" hint="Kosongkan bila tidak ada." />
        <x-input label="No. WhatsApp" name="no_wa" :value="$pencari?->no_wa ?? $waDefault" :required="$waWajib ?? true" />
    </div>
</x-card>

<x-card title="Alamat (Kabupaten Timor Tengah Selatan)">
    <x-wilayah-select :kecamatan="$kecamatanOptions" :kecamatan-id="$pencari?->kecamatan_id" :desa-id="$pencari?->desa_id" />
    <x-textarea label="Alamat detail (RT/RW, dusun, patokan)" name="alamat_detail" :value="$pencari?->alamat_detail" required class="mt-4" />
</x-card>

<x-card title="Minat & Kemampuan">
    <div class="space-y-4">
        <x-checkboxes label="Kategori minat kerja" name="kategori" :options="$kategoriOptions" :selected="$pencari?->kategori->pluck('id')->all() ?? []" required />
        <x-checkboxes label="Kemampuan (skill)" name="skills" :options="$skillOptions" :selected="$pencari?->skills->pluck('id')->all() ?? []" />
        <x-textarea label="Kemampuan lain" name="kemampuan_lain" :value="$pencari?->kemampuan_lain" hint="Tulis kemampuan lain yang tidak ada di daftar." />
        <x-textarea label="Pengalaman kerja" name="pengalaman_kerja" :value="$pencari?->pengalaman_kerja" rows="4" hint="Contoh: 2 tahun bekerja di bengkel motor." />
        <x-file-input label="Upload CV (PDF)" name="cv" accept=".pdf" hint="Opsional. PDF maksimal 2 MB."
            :current="$pencari?->cv_path ? route('berkas.cv', $pencari) : null" />
    </div>
</x-card>
