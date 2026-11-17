@php($baru = ! $pelatihan->exists)
<x-layouts.app :title="$baru ? 'Buat Pelatihan' : 'Ubah Pelatihan'">
    <x-page-header :title="$baru ? 'Buat Pelatihan' : 'Ubah Pelatihan'" :back="$baru ? route('admin.pelatihan.index') : route('admin.pelatihan.show', $pelatihan)" />

    <form method="POST" action="{{ $baru ? route('admin.pelatihan.store') : route('admin.pelatihan.update', $pelatihan) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <x-card title="Informasi Pelatihan">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Nama pelatihan" name="nama" :value="$pelatihan->nama" required class="sm:col-span-2" />
                <x-select label="Kategori" name="kategori_id" :options="$kategori" :value="$pelatihan->kategori_id" required />
                <x-input label="Kuota peserta" name="kuota" type="number" min="1" :value="$pelatihan->kuota" required />
                <x-input label="Penyelenggara" name="penyelenggara" :value="$pelatihan->penyelenggara" required />
                <x-input label="Mitra (opsional)" name="mitra" :value="$pelatihan->mitra" hint="Contoh: BLK Kupang, Dinas Pertanian." />
                <x-textarea label="Deskripsi" name="deskripsi" :value="$pelatihan->deskripsi" rows="4" required class="sm:col-span-2" />
                <x-textarea label="Syarat peserta" name="syarat" :value="$pelatihan->syarat" class="sm:col-span-2" />
            </div>
        </x-card>

        <x-card title="Jadwal & Lokasi">
            <div class="grid gap-4 sm:grid-cols-3">
                <x-input label="Tanggal mulai" name="tanggal_mulai" type="date" :value="$pelatihan->tanggal_mulai?->format('Y-m-d')" required />
                <x-input label="Tanggal selesai" name="tanggal_selesai" type="date" :value="$pelatihan->tanggal_selesai?->format('Y-m-d')" />
                <x-input label="Batas pendaftaran" name="batas_pendaftaran" type="date" :value="$pelatihan->batas_pendaftaran?->format('Y-m-d')" required />
                <x-input label="Jam mulai" name="jam_mulai" type="time" :value="$pelatihan->jam_mulai ? substr($pelatihan->jam_mulai, 0, 5) : null" required />
                <x-input label="Jam selesai" name="jam_selesai" type="time" :value="$pelatihan->jam_selesai ? substr($pelatihan->jam_selesai, 0, 5) : null" />
                <x-select label="Kecamatan" name="kecamatan_id" :options="$kecamatan" :value="$pelatihan->kecamatan_id" required />
                <x-textarea label="Alamat lokasi" name="alamat" :value="$pelatihan->alamat" required class="sm:col-span-3" />
            </div>
        </x-card>

        <x-card title="Sertifikat & Poster">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="bersertifikat" value="1" class="rounded border-slate-300" @checked(old('bersertifikat', $pelatihan->bersertifikat))>
                    Ada sertifikat untuk peserta yang lulus
                </label>
                <x-file-input label="Poster" name="poster" accept=".jpg,.jpeg,.png,.webp" hint="Gambar maksimal 2 MB. Tampil di halaman publik." :current="$pelatihan->posterUrl()" />
            </div>
        </x-card>

        <x-button size="lg">Simpan</x-button>
    </form>
</x-layouts.app>
