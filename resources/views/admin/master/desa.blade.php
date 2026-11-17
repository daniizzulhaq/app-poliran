<x-layouts.app :title="'Desa - '.$kecamatan->nama">
    <x-page-header :title="'Desa di Kecamatan '.$kecamatan->nama" :back="route('admin.kecamatan.index')" />

    <x-card class="mb-4 max-w-2xl">
        <form method="POST" action="{{ route('admin.desa.store', $kecamatan) }}" class="flex flex-wrap items-end gap-2">
            @csrf
            <x-input label="Nama desa / kelurahan" name="nama" required class="min-w-56 flex-1" />
            <x-input label="Kode (opsional)" name="kode" class="w-32" />
            <x-button>Tambah</x-button>
        </form>
    </x-card>

    <x-table :paginator="$desa" class="max-w-3xl">
        <x-slot:head><th>Desa / Kelurahan</th><th>Kode</th><th class="text-right">Aksi</th></x-slot:head>
        @forelse ($desa as $item)
            <tr x-data="{ ubah: false }">
                <td>
                    <span x-show="!ubah">{{ $item->nama }}</span>
                    <form x-show="ubah" x-cloak method="POST" action="{{ route('admin.desa.update', [$kecamatan, $item]) }}" class="flex flex-wrap gap-2">
                        @csrf @method('PUT')
                        <input name="nama" value="{{ $item->nama }}" class="form-control w-48" required>
                        <input name="kode" value="{{ $item->kode }}" class="form-control w-24" placeholder="Kode">
                        <x-button size="sm">Simpan</x-button>
                    </form>
                </td>
                <td>{{ $item->kode ?? '-' }}</td>
                <td class="text-right whitespace-nowrap">
                    <button type="button" class="text-brand-600 hover:underline" @click="ubah = !ubah" x-text="ubah ? 'Batal' : 'Ubah'"></button>
                    <form method="POST" action="{{ route('admin.desa.destroy', [$kecamatan, $item]) }}" class="inline" onsubmit="return confirm('Hapus desa ini?')">
                        @csrf @method('DELETE')
                        <button class="ml-3 text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3"><x-empty /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
