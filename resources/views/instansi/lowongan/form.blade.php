{{--
    Form lowongan. Dipakai instansi (upload sendiri) dan petugas (jemput bola, $petugas = true):
    petugas memilih instansi, lowongan langsung aktif, dan untuk luar TTS / luar negeri wajib centang legalitas.
--}}
@php($baru = ! $lowongan->exists)
@php($petugas = $petugas ?? false)
@php($judulHalaman = $petugas ? 'Input Lowongan (Petugas)' : ($baru ? 'Upload Lowongan' : 'Ubah Lowongan'))
<x-layouts.app :title="$judulHalaman">
    <x-page-header :title="$judulHalaman" :back="$petugas ? route('admin.lowongan.index') : route('instansi.lowongan.index')"
        :subtitle="$petugas
            ? 'Untuk lowongan yang didapat petugas langsung dari instansi terverifikasi. Lowongan langsung aktif atas nama Anda sebagai petugas survei.'
            : 'Lowongan akan diperiksa petugas Polres (gaji wajar, tidak meminta uang, bukan penipuan) sebelum aktif.'" />

    @if ($lowongan->alasan_tolak)
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"><strong>Alasan ditolak:</strong> {{ $lowongan->alasan_tolak }}</div>
    @endif

    <form method="POST" class="space-y-4"
        action="{{ $petugas ? route('admin.lowongan.store') : ($baru ? route('instansi.lowongan.store') : route('instansi.lowongan.update', $lowongan)) }}"
        x-data="{ wilayah: @js(old('wilayah_kerja', $lowongan->wilayah_kerja?->value ?? \App\Enums\WilayahKerja::DalamTts->value)) }">
        @csrf
        @unless ($baru || $petugas) @method('PUT') @endunless

        <x-card title="Pekerjaan">
            <div class="grid gap-4 sm:grid-cols-2">
                @if ($petugas)
                    <x-select label="Instansi (terverifikasi)" name="instansi_id" :options="$instansiOptions" :value="request('instansi_id')" required class="sm:col-span-2"
                        hint="Belum ada di daftar? Input dulu instansinya lewat Survei Instansi > Tambah Instansi." />
                @endif
                <x-input label="Judul lowongan" name="judul" :value="$lowongan->judul" required placeholder="Contoh: Tukang batu" />
                <x-select label="Kategori" name="kategori_id" :options="$kategori" :value="$lowongan->kategori_id" required />
                <x-select label="Tipe kerja" name="tipe_kerja" :options="$tipeKerja" :value="$lowongan->tipe_kerja" required />
                <x-input label="Lokasi kerja" name="lokasi_kerja" :value="$lowongan->lokasi_kerja" required placeholder="Contoh: Kota Soe / Kupang / Malaysia" />
                <x-select label="Wilayah kerja" name="wilayah_kerja" :options="$wilayahKerja" :value="$lowongan->wilayah_kerja" required :placeholder="false" x-model="wilayah" />
                <div x-show="wilayah === '{{ \App\Enums\WilayahKerja::LuarNegeri->value }}'" x-cloak>
                    <x-input label="Nomor izin perusahaan penempatan (P3MI)" name="nomor_izin_p3mi" :value="$lowongan->nomor_izin_p3mi"
                        hint="Wajib untuk kerja luar negeri. Petugas akan memeriksa keabsahannya." />
                </div>
                <div x-show="wilayah !== '{{ \App\Enums\WilayahKerja::DalamTts->value }}'" x-cloak class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900 sm:col-span-2">
                    Lowongan di luar TTS diperiksa lebih ketat untuk mencegah calo dan perdagangan orang:
                    kontrak tertulis, biaya perjalanan tidak dipotong dari gaji, dan pekerja tidak menyerahkan KTP/ijazah asli.
                </div>
                <x-textarea label="Jobdesk (tugas pekerjaan)" name="jobdesk" :value="$lowongan->jobdesk" rows="4" required class="sm:col-span-2" />
                <x-checkboxes label="Skill yang dibutuhkan" name="skills" :options="$skills" :selected="$lowongan->exists ? $lowongan->skills->pluck('id')->all() : []" class="sm:col-span-2" />
            </div>
        </x-card>

        <x-card title="Syarat">
            <div class="grid gap-4 sm:grid-cols-3">
                <x-select label="Pendidikan minimal" name="pendidikan_min" :options="$pendidikan" :value="$lowongan->pendidikan_min" placeholder="Tanpa syarat" />
                <x-input label="Umur minimal" name="umur_min" type="number" min="15" max="70" :value="$lowongan->umur_min" />
                <x-input label="Umur maksimal" name="umur_max" type="number" min="15" max="70" :value="$lowongan->umur_max" />
                <x-select label="Jenis kelamin" name="jenis_kelamin" :options="$jenisKelamin" :value="$lowongan->jenis_kelamin" placeholder="Laki-laki / Perempuan" />
                <x-textarea label="Pengalaman" name="pengalaman" :value="$lowongan->pengalaman" class="sm:col-span-2" />
                <x-textarea label="Syarat lain" name="syarat_lain" :value="$lowongan->syarat_lain" class="sm:col-span-3" />
            </div>
        </x-card>

        <x-card title="Gaji, Fasilitas & Kuota">
            <div class="grid gap-4 sm:grid-cols-3">
                <x-input label="Gaji minimal (Rp)" name="gaji_min" type="number" min="0" :value="$lowongan->gaji_min" />
                <x-input label="Gaji maksimal (Rp)" name="gaji_max" type="number" min="0" :value="$lowongan->gaji_max" />
                <label class="flex items-center gap-2 pt-6 text-sm">
                    <input type="checkbox" name="gaji_nego" value="1" class="rounded border-slate-300" @checked(old('gaji_nego', $lowongan->gaji_nego))> Gaji bisa nego
                </label>
                <x-textarea label="Fasilitas" name="fasilitas" :value="$lowongan->fasilitas" class="sm:col-span-3" />
                <x-textarea label="Ketentuan lain" name="ketentuan_lain" :value="$lowongan->ketentuan_lain" class="sm:col-span-3" />
                <x-input label="Kuota (jumlah orang)" name="kuota" type="number" min="1" :value="$lowongan->kuota" required />
                <x-input label="Batas tanggal" name="batas_tanggal" type="date" :value="$lowongan->batas_tanggal?->format('Y-m-d')" required />
            </div>
        </x-card>

        @if ($petugas)
            <x-card title="Hasil Pemeriksaan Petugas">
                @foreach ([\App\Enums\WilayahKerja::LuarTts, \App\Enums\WilayahKerja::LuarNegeri] as $w)
                    <div x-show="wilayah === '{{ $w->value }}'" x-cloak class="mb-3">
                        <p class="mb-1 text-sm font-medium">Daftar periksa {{ mb_strtolower($w->label()) }}:</p>
                        <ul class="list-disc space-y-1 pl-5 text-xs text-slate-600">
                            @foreach ($w->daftarPeriksa() as $poin)<li>{{ $poin }}</li>@endforeach
                        </ul>
                    </div>
                @endforeach
                <label x-show="wilayah !== '{{ \App\Enums\WilayahKerja::DalamTts->value }}'" x-cloak class="mb-3 flex items-start gap-2 text-sm">
                    <input type="checkbox" name="legalitas_diperiksa" value="1" @checked(old('legalitas_diperiksa')) class="mt-0.5 rounded border-slate-300">
                    <span>Saya sudah memeriksa legalitas sesuai daftar periksa di atas.</span>
                </label>
                <x-textarea label="Catatan survei" name="catatan_survei" rows="3" hint="Contoh: bertemu langsung dengan pemilik usaha, gaji sesuai UMK." />
            </x-card>
        @endif

        <x-button size="lg">{{ $petugas ? 'Simpan & Aktifkan' : ($baru ? 'Kirim untuk Disurvei' : 'Simpan & Kirim Ulang') }}</x-button>
    </form>
</x-layouts.app>
