@php($baru = ! $testimoni->exists)
<x-layouts.app :title="$baru ? 'Tambah Cerita Sukses' : 'Ubah Cerita Sukses'">
    <x-page-header :title="$baru ? 'Tambah Cerita Sukses' : 'Ubah Cerita Sukses'" :back="route('admin.testimoni.index')" />
    <x-card class="max-w-2xl">
        <form method="POST" action="{{ $baru ? route('admin.testimoni.store') : route('admin.testimoni.update', $testimoni) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @unless ($baru) @method('PUT') @endunless
            <x-input label="Nama" name="nama" :value="$testimoni->nama" required />
            <x-input label="Keterangan" name="keterangan" :value="$testimoni->keterangan" hint="Contoh: Disalurkan sebagai tukang las, Kec. ..." />
            <x-textarea label="Cerita" name="isi" :value="$testimoni->isi" rows="4" required />
            <x-file-input label="Foto (opsional)" name="foto" accept=".jpg,.jpeg,.png,.webp" :current="$testimoni->fotoUrl()" />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Urutan" name="urutan" type="number" min="0" :value="$testimoni->urutan" />
                <label class="flex items-center gap-2 pt-6 text-sm">
                    <input type="checkbox" name="aktif" value="1" class="rounded border-slate-300" @checked(old('aktif', $testimoni->aktif))> Tampilkan di halaman awal
                </label>
            </div>
            <x-button>Simpan</x-button>
        </form>
    </x-card>
</x-layouts.app>
