@php($baru = ! $lowongan->exists)
<x-layouts.app :title="$baru ? 'Upload Lowongan' : 'Ubah Lowongan'">
    <x-page-header :title="$baru ? 'Upload Lowongan' : 'Ubah Lowongan'" :back="route('instansi.lowongan.index')"
        subtitle="Lowongan akan diperiksa petugas Polres (gaji wajar, tidak meminta uang, bukan penipuan) sebelum aktif." />

    @if ($lowongan->alasan_tolak)
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"><strong>Alasan ditolak:</strong> {{ $lowongan->alasan_tolak }}</div>
    @endif

    <form method="POST" action="{{ $baru ? route('instansi.lowongan.store') : route('instansi.lowongan.update', $lowongan) }}" class="space-y-4">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <x-card title="Pekerjaan">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Judul lowongan" name="judul" :value="$lowongan->judul" required placeholder="Contoh: Tukang batu" />
                <x-select label="Kategori" name="kategori_id" :options="$kategori" :value="$lowongan->kategori_id" required />
                <x-select label="Tipe kerja" name="tipe_kerja" :options="$tipeKerja" :value="$lowongan->tipe_kerja" required />
                <x-input label="Lokasi kerja" name="lokasi_kerja" :value="$lowongan->lokasi_kerja" required />
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

        <x-button size="lg">{{ $baru ? 'Kirim untuk Disurvei' : 'Simpan & Kirim Ulang' }}</x-button>
    </form>
</x-layouts.app>
