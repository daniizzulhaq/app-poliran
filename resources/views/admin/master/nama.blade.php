<x-layouts.app :title="$judul">
    <x-page-header :title="$judul" :subtitle="$keterangan" />

    <x-card class="mb-4 max-w-xl">
        <form method="POST" action="{{ route($route.'.store') }}" class="flex flex-wrap items-end gap-2">
            @csrf
            <x-input label="Tambah {{ strtolower($judul) }} baru" name="nama" required class="min-w-56 flex-1" />
            <x-button>Tambah</x-button>
        </form>
    </x-card>

    <x-table :paginator="$items" class="max-w-3xl">
        <x-slot:head><th>Nama</th><th class="text-right">Aksi</th></x-slot:head>
        @forelse ($items as $item)
            <tr x-data="{ ubah: false }">
                <td>
                    <span x-show="!ubah">{{ $item->nama }}</span>
                    <form x-show="ubah" x-cloak method="POST" action="{{ route($route.'.update', $item) }}" class="flex gap-2">
                        @csrf @method('PUT')
                        <input name="nama" value="{{ $item->nama }}" class="form-control" required>
                        <x-button size="sm">Simpan</x-button>
                    </form>
                </td>
                <td class="text-right whitespace-nowrap">
                    <button type="button" class="text-brand-600 hover:underline" @click="ubah = !ubah" x-text="ubah ? 'Batal' : 'Ubah'"></button>
                    <form method="POST" action="{{ route($route.'.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus {{ e($item->nama) }}?')">
                        @csrf @method('DELETE')
                        <button class="ml-3 text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="2"><x-empty /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
