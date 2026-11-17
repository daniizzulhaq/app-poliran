<x-layouts.app title="Kecamatan & Desa">
    <x-page-header title="Kecamatan & Desa" subtitle="Wilayah Kabupaten Timor Tengah Selatan. Data awal bisa diimpor dari database/data/wilayah_tts.csv." />

    <x-card class="mb-4 max-w-2xl">
        <form method="POST" action="{{ route('admin.kecamatan.store') }}" class="flex flex-wrap items-end gap-2">
            @csrf
            <x-input label="Nama kecamatan" name="nama" required class="min-w-56 flex-1" />
            <x-input label="Kode (opsional)" name="kode" class="w-32" />
            <x-button>Tambah</x-button>
        </form>
    </x-card>

    <x-table :paginator="$kecamatan" class="max-w-4xl">
        <x-slot:head><th>Kecamatan</th><th>Kode</th><th>Jumlah Desa</th><th class="text-right">Aksi</th></x-slot:head>
        @forelse ($kecamatan as $item)
            <tr x-data="{ ubah: false }">
                <td>
                    <a x-show="!ubah" href="{{ route('admin.kecamatan.show', $item) }}" class="font-medium text-brand-700 hover:underline">{{ $item->nama }}</a>
                    <form x-show="ubah" x-cloak method="POST" action="{{ route('admin.kecamatan.update', $item) }}" class="flex flex-wrap gap-2">
                        @csrf @method('PUT')
                        <input name="nama" value="{{ $item->nama }}" class="form-control w-48" required>
                        <input name="kode" value="{{ $item->kode }}" class="form-control w-24" placeholder="Kode">
                        <x-button size="sm">Simpan</x-button>
                    </form>
                </td>
                <td>{{ $item->kode ?? '-' }}</td>
                <td>{{ $item->desa_count }}</td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('admin.kecamatan.show', $item) }}" class="text-brand-600 hover:underline">Desa</a>
                    <button type="button" class="ml-3 text-brand-600 hover:underline" @click="ubah = !ubah" x-text="ubah ? 'Batal' : 'Ubah'"></button>
                    <form method="POST" action="{{ route('admin.kecamatan.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus kecamatan beserta desanya?')">
                        @csrf @method('DELETE')
                        <button class="ml-3 text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4"><x-empty /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
